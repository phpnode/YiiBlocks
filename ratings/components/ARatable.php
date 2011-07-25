<?php
/**
 * Allows models to be rated by users.
 * @author Charles Pick
 * @package application.modules.admin.components
 */
class ARatable extends CActiveRecordBehavior implements IARatable {
	/**
	 * Whether the current user has rated this item or not
	 * @var boolean
	 */
	protected $_userHasRated;
	
	/**
	 * Contains the rating score if the current user has rated.
	 * Usually 0 to 10
	 * @var integer
	 */
	protected $_userRatingScore;
	
	/**
	 * Gets the id of the object being moderated.
	 * @return integer the id of the object being moderated.
	 */
	public function getId() {
		return $this->owner->primaryKey;
	}
	
	/**
	 * Gets the name of the class that is being moderated.
	 * @return string the owner model class name
	 */
	public function getClassName() {
		return get_class($this->owner);
	}
	
	/**
	 * Gets the data provider for this set of ratings
	 * @return CActiveDataProvider The dataProvider that retrieves the ratings
	 */
	public function getRatingDataProvider() {
		$dataProvider = new CActiveDataProvider(ARating::model()->ownedBy($this->owner));
		return $dataProvider;
	}
	
	/**
	 * Adds a rating to the owner model
	 * @param Rating $rating The rating to add
	 * @param boolean $runValidation Whether to run validation or not
	 * @return boolean Whether the save succeeded or not
	 */
	public function addRating(ARating $rating, $runValidation = true) {
		
		$rating->ownerModel = $this->getClassName();
		$rating->ownerId = $this->getId();
		return $rating->save($runValidation);
	}
	
	/**
	 * Resets the rating for the current user for the owner model.
	 * If {@link ARatingManager::requiresLogin} is set to true and
	 * the current user is not logged in, false will be return.
	 * @return boolean whether the delete was successful or not
	 */
	public function resetRating() {
		$attributes = array(
			"ownerModel" => $this->getClassName(),
			"ownerId" => $this->getId(),
		);
		if (Yii::app()->getModule('ratings')->requiresLogin) {
			if (Yii::app()->user->isGuest) {
				return false;
			}
			$attributes['ratingId'] = Yii::app()->user->id;
		}
		else {
			$attributes['ratingIP'] = $_SERVER['REMOTE_ADDR'];
			$attributes['ratingUserAgent'] = $_SERVER['HTTP_USER_AGENT'];
		}
		$model = ARating::model()->findByAttributes($attributes);
		if (is_object($model)) { 
			return $model->delete();
		}
		
	}
	
	/**
	 * Named Scope: Orders a list of models by the most highly rated first
	 * @return CActiveRecord The owner object with the scope applied
	 */
	public function mostPopular() {
		$ratingTable = Yii::app()->getModule('ratings')->ratingTable;
		$id = $this->owner->tableSchema->primaryKey;
		$criteria = new CDbCriteria;
		$criteria->select = "t.*, SUM($ratingTable.score) AS ratingScore";
		$criteria->join = "LEFT JOIN $ratingTable ON $ratingTable.ownerModel = :ratingOwnerModel AND $ratingTable.ownerId = t.$id";
		$criteria->group = "t.".$id;
		$criteria->params = array(
				":ratingOwnerModel" => $this->getClassName(),
			);
		$criteria->order = "ratingScore DESC";
		$this->owner->getDbCriteria()->mergeWith($criteria);
		return $this->owner;
	}
	
	/**
	 * Named Scope: Orders a list of models by the least popular first
	 * @return CActiveRecord The owner object with the scope applied
	 */
	public function leastPopular() {
		$ratingTable = Yii::app()->getModule('ratings')->ratingTable;
		$criteria = new CDbCriteria;
		$criteria->select = "t.*, SUM($ratingTable.score) AS ratingScore";
		$criteria->join = "LEFT JOIN $ratingTable ON $ratingTable.ownerModel = :ratingOwnerModel AND $ratingTable.ownerId = t.$id";
		$criteria->group = "t.".$id;
		$criteria->params = array(
				":ratingOwnerModel" => $this->getClassName(),
			);
		$criteria->order = "ratingScore ASC";
		$this->owner->getDbCriteria()->mergeWith($criteria);
		return $this->owner;
	}
	/**
	 * Gets the configuration for a STAT relation with the total number of ratings for this model.
	 * This should be added to the relations() definition in the owner model.
	 * <pre>
	 * "totalRatings" => ARatable::totalRatingsRelation(__CLASS__)
	 * </pre>
	 * @param string $className the name of the class
	 * @return array the relation configuration
	 */
	public static function totalRatingsRelation($className) {
		return array(
				CActiveRecord::STAT,
				"ARating",
				"ownerId",
				"condition" => "ownerModel = :ratingOwnerModel",
				"params" => array(
					":ratingOwnerModel" => $className
				)
			);
	}
	
	/**
	 * Gets the configuration for a STAT relation with the total rating score for this model.
	 * This should be added to the relations() definition in the owner model.
	 * <pre>
	 * "totalRatingScore" => ARatable::totalRatingScoreRelation(__CLASS__)
	 * </pre>
	 * @param string $className the name of the class
	 * @return array the relation configuration
	 */
	public static function totalRatingScoreRelation($className) {
		return array(
				CActiveRecord::STAT,
				"ARating",
				"ownerId",
				"select" => "IFNULL(SUM(score), 0)",
				"condition" => "ownerModel = :ratingOwnerModel",
				"params" => array(
					":ratingOwnerModel" => $className
				)
			);
	}
	
	/**
	 * Gets the configuration for a STAT relation with the average rating score for this model.
	 * This should be added to the relations() definition in the owner model.
	 * <pre>
	 * "averageRatingScore" => ARatable::averageRatingScoreRelation(__CLASS__)
	 * </pre>
	 * @param string $className the name of the class
	 * @return array the relation configuration
	 */
	public static function averageRatingScoreRelation($className) {
		return array(
				CActiveRecord::STAT,
				"ARating",
				"ownerId",
				"select" => "(IFNULL(SUM(score), 0)) / COUNT(*)",
				"condition" => "ownerModel = :ratingOwnerModel",
				"params" => array(
					":ratingOwnerModel" => $className
				)
			);
	}
	
	/**
	 * Gets the configuration for a STAT relation with whether the current user has rated this model
	 * This should be added to the relations() definition in the owner model.
	 * <pre>
	 * "userHasRated" => ARatable::userHasRatedRelation(__CLASS__)
	 * </pre>
	 * @param string $className the name of the class
	 * @return array the relation configuration
	 */
	public static function userHasRatedRelation($className) {
		$relation =  array(
				CActiveRecord::STAT,
				"ARating",
				"ownerId",
				"condition" => "ownerModel = :ratingOwnerModel",
				"params" => array(
					":ratingOwnerModel" => $className
				)
			);
		if (Yii::app()->getModule('ratings')->requiresLogin) {
			$relation['condition'] .= " AND userHasRated.ratingerId = :ratingerId";
			$relation['params'][":ratingerId"] = Yii::app()->user->id;
		}
		else {
			$relation['condition'] .= " AND userHasRated.ratingerIP = :ratingerIP AND userHasRated.ratingerUserAgent = :ratingerUserAgent";
			$relation['params'][":ratingerIP"] = $_SERVER['REMOTE_ADDR'];
			$relation['params'][":ratingerUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
		}
		return $relation;
	}
	
	/**
	 * Gets the configuration for a HAS_ONE relation which returns the rating for this item by the current user
	 * This should be added to the relations() definition in the owner model.
	 * <pre>
	 * "userRating" => ARatable::userRatingRelation(__CLASS__)
	 * </pre>
	 * @param string $className the name of the class
	 * @return array the relation configuration
	 */
	public static function userRatingRelation($className) {
		$relation =  array(
				CActiveRecord::HAS_ONE,
				"ARating",
				"ownerId",
				"condition" => "ownerModel = :ratingOwnerModel",
				"params" => array(
					":ratingOwnerModel" => $className
				)
			);
		if (Yii::app()->getModule('ratings')->requiresLogin) {
			$relation['condition'] .= " AND userRating.ratingerId = :ratingerId";
			$relation['params'][":ratingerId"] = Yii::app()->user->id;
		}
		else {
			$relation['condition'] .= " AND userRating.ratingerIP = :ratingerIP AND userRating.ratingerUserAgent = :ratingerUserAgent";
			$relation['params'][":ratingerIP"] = $_SERVER['REMOTE_ADDR'];
			$relation['params'][":ratingerUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
		}
		return $relation;
	}
	
	/**
	 * Gets the configuration for a HAS_MANY relation which returns the ratings for this item
	 * This should be added to the relations() definition in the owner model.
	 * <pre>
	 * "ratings" => ARatable::ratingsRelation(__CLASS__)
	 * </pre>
	 * @param string $className the name of the class
	 * @return array the relation configuration
	 */
	public static function ratingsRelation($className) {
		$relation =  array(
				CActiveRecord::HAS_MANY,
				"ARating",
				"ownerId",
				"condition" => "ratings.ownerModel = :ratingOwnerModel",
				"params" => array(
					":ratingOwnerModel" => $className
				)
			);
		return $relation;
	}
	/**
	 * Provides easy drop in relations for ratable models.
	 * Usage:
	 * <pre>
	 * public function relations() {
	 * 	return CMap::mergeArray(ARatable::relations(__CLASS__),array(
	 * 		"someRelation" => array(self::HAS_MANY,"blah","something")
	 * 	));
	 * }
	 * </pre>
	 * @param string $className the name of the class
	 * @return array The relations provided by this behavior
	 */
	public static function relations($className) {
		return array(
			"totalRatings" => self::totalRatingsRelation($className),
			"totalRatingScore" => self::totalRatingScoreRelation($className),
			"averageRating" => self::averageRatingScoreRelation($className),
			"userHasRated" => self::userHasRatedRelation($className),
			"userRating" => self::userRatingRelation($className),
			"ratings" => self::ratingsRelation($className),
		);
	}
}
