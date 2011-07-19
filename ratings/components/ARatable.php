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
	 * Named Scope: Return the rating information with the main query to
	 * save additional rating lookup queries later.
	 * To use this the owner model have the required public properties:
	 * $_userHasRated,
	 * $_userRatingScore,
	 * $_totalRatings and
	 * $_totalRatingScore
	 * @return CActiveRecord The owner object
	 */
	public function withRatingInfo() {
		if (
			!property_exists($this->owner,"_userHasRated") ||
			!property_exists($this->owner,"_userRatingScore") ||
			!property_exists($this->owner,"_totalRatings") || 
			!property_exists($this->owner,"_totalRatingScore") 
			) {
			throw new CException(get_class($this->owner)." does not have the required public properties, \$_userHasRated, \$_userRatingScore, \$_totalRatings and \$_totalRatingScore");
		}
		
		$ratingTable = Yii::app()->getModule('ratings')->ratingTable;
		$id = $this->owner->tableSchema->primaryKey;
		$criteria = new CDbCriteria;
		$criteria->join = " LEFT JOIN $ratingTable ON $ratingTable.ownerModel = :ratingOwnerModel AND $ratingTable.ownerId = t.$id";
		$criteria->select = "t.*,
							IF($ratingTable.id IS NULL, 0, 1) AS _userHasRated,
							IF($ratingTable.score IS NULL, 0, $ratingTable.score) AS _userRatingScore,
							(SELECT COUNT($ratingTable.id) + 1 FROM $ratingTable WHERE $ratingTable.ownerModel = :ratingOwnerModel AND $ratingTable.ownerId = t.id) as _totalRatings,
							IFNULL((SELECT SUM($ratingTable.score) FROM $ratingTable WHERE $ratingTable.ownerModel = :ratingOwnerModel AND $ratingTable.ownerId = t.id), 0) + 1 as _totalRatingScore
							";
		
		
		if (Yii::app()->getModule('ratings')->requiresLogin) {
			
			$criteria->join .= " AND $ratingTable.ratingId = :ratingId";
			$criteria->params[":ratingId"] = Yii::app()->user->id;
		}
		else {
			$criteria->join .= " AND $ratingTable.ratingIP = :ratingIP AND $ratingTable.ratingUserAgent = :ratingUserAgent";
			$criteria->params[":ratingIP"] = $_SERVER['REMOTE_ADDR'];
			$criteria->params[":ratingUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
		}
		$criteria->params[":ratingOwnerModel"] = get_class($this->owner);
		
		$this->owner->getDbCriteria()->mergeWith($criteria);
		
		return $this->owner;
	}
	
	/**
	 * Determine whether the current user has rated this item or not. 
	 * @return boolean whether the current use has rated or not
	 */
	public function getUserHasRated() {
		if (Yii::app()->getModule('ratings')->requiresLogin && Yii::app()->user->isGuest) {
			return false;
		}
		if (property_exists($this->owner,"_userHasRated")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		if ($object->_userHasRated === null) {
			$criteria = new CDbCriteria;
			if (Yii::app()->getModule('ratings')->requiresLogin) {
				$criteria->addCondition("ratingId = :ratingId");
				$criteria->params[":ratingId"] = Yii::app()->user->id;
			}
			else {
				$criteria->addCondition("ratingIP = :ratingIP AND ratingUserAgent = :ratingUserAgent");
				$criteria->params[":ratingIP"] = $_SERVER['REMOTE_ADDR'];
				$criteria->params[":ratingUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
			}
			$rating = ARating::model()->ownedBy($this->owner)->find($criteria);
			if (is_object($rating)) {
				$object->_userHasRated = true;
				if ($object->_userRatingScore === null) {
					$object->_userRatingScore = $rating->score;
				}
			}
			else {
				$object->_userHasRated = false;
			}
			
		}
		return $object->_userHasRated;
	}
	/**
	 * Sets whether the user has rated or not.
	 * This is mainly used internally, it does not modify anything permanently.
	 * @param boolean $value Whether the user has rated or not
	 */
	public function setUserHasRated($value) {
		if (property_exists($this->owner,"_userHasRated")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		$object->_userHasRated = (bool) $value;
	}
	
	/**
	 * Get the score given by the user for this item if they have rated.
	 * If they have not rated, returns false
	 * @return mixed integer if the user has rated, otherwise false.
	 */
	public function getUserRatingScore() {
		if (Yii::app()->getModule('ratings')->requiresLogin && Yii::app()->user->isGuest) {
			return false;
		}
		if (property_exists($this->owner,"_userHasRated")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		if ($object->_userRatingScore === null) {
			$criteria = new CDbCriteria;
			if (Yii::app()->getModule('ratings')->requiresLogin) {
				$criteria->addCondition("ratingId = :ratingId");
				$criteria->params[":ratingId"] = Yii::app()->user->id;
			}
			else {
				$criteria->addCondition("ratingIP = :ratingIP AND ratingUserAgent = :ratingUserAgent");
				$criteria->params[":ratingIP"] = $_SERVER['REMOTE_ADDR'];
				$criteria->params[":ratingUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
			}
			$rating = ARating::model()->ownedBy($this->owner)->find($criteria);
			if (is_object($rating)) {
				$object->_userRatingScore = $rating->score;
				if ($object->_userHasRated === null) {
					$object->_userHasRated = true;
				}
			}
			else {
				$object->_userRatingScore = false;
				if ($object->_userHasRated === null) {
					$object->_userHasRated = false;
				}
			}
			
		}
		return $object->_userRatingScore;
	}
	
	
	/**
	 * Sets the user rating score
	 * This is mainly used internally, it does not modify anything permanently.
	 * @param integer $value The score given by the user
	 */
	public function setUserRatingScore($value) {
		if (property_exists($this->owner,"_userHasRated")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		$object->_userRatingScore = $value;
	}
	
	/**
	 * Gets the total score for this votable item.
	 * @return integer The score
	 */
	public function getTotalRatingScore() {
		if (property_exists($this->owner,"_totalRatingScore")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		if ($object->_totalRatingScore === null) {
			$ratingTable = Yii::app()->getModule('ratings')->ratingTable;
			$id = $this->owner->tableSchema->primaryKey;
			$sql = "SELECT COUNT($ratingTable.id) AS _totalRatings, IFNULL(SUM($ratingTable.score), 0) + 1  as _totalRatingScore FROM $ratingTable WHERE $ratingTable.ownerModel = :ratingOwnerModel AND $ratingTable.ownerId = :ratingOwnerId";
			$cmd = Yii::app()->db->createCommand($sql);
			$cmd->bindValues(array(
				":ratingOwnerModel" => $this->getClassName(),
				":ratingOwnerId" => $this->getId(),
			));
			$row = $cmd->queryRow();
			$object->_totalRatingScore = $row['_totalRatingScore'];
			$object->_totalRatings = $row['_totalRatings'];
		}	
		return $object->_totalRatingScore;	
	}
	
	
	/**
	 * Gets the total number of ratings for this item
	 * @return integer The total number of ratings for this item
	 */
	public function getTotalRatings() {
		if (property_exists($this->owner,"_totalRatingScore")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		if ($object->_totalRatings === null) {
			$ratingTable = Yii::app()->getModule('ratings')->ratingTable;
			$id = $this->owner->tableSchema->primaryKey;
			$sql = "SELECT COUNT($ratingTable.id) AS _totalRatings, IFNULL(SUM($ratingTable.score), 0) + 1  as _totalRatingScore FROM $ratingTable WHERE $ratingTable.ownerModel = :ratingOwnerModel AND $ratingTable.ownerId = :ratingOwnerId";
			$cmd = Yii::app()->db->createCommand($sql);
			$cmd->bindValues(array(
				":ratingOwnerModel" => $this->getClassName(),
				":ratingOwnerId" => $this->getId(),
			));
			$row = $cmd->queryRow();
			$object->_totalRatingScore = $row['_totalRatingScore'];
			$object->_totalRatings = $row['_totalRatings'];
		}	
		return $object->_totalRatings;	
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
	 * Upratings the owner model.
	 * If {@link ARatingManager::requiresLogin} is set to true and
	 * the current user is not logged in, false will be return.
	 * @return boolean whether the save was successful or not
	 */
	public function upRating() {
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
		if (!is_object($model)) { 
			$model = new ARating;
			foreach($attributes as $key => $value) {
				$model->{$key} = $value;
			}
		}		
		$model->score = 1;
		return $model->save();
	}
	
	/**
	 * Downratings the owner model.
	 * If {@link ARatingManager::requiresLogin} is set to true and
	 * the current user is not logged in, false will be return.
	 * @return boolean whether the save was successful or not
	 */
	public function downRating() {
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
		if (!is_object($model)) { 
			$model = new ARating;
			foreach($attributes as $key => $value) {
				$model->{$key} = $value;
			}
		}		
		$model->score = -1;
		return $model->save();
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
}
