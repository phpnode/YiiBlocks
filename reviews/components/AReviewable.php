<?php
Yii::import("packages.reviews.interfaces.IAReviewable");
/**
 * Allows models to be reviewed by users
 * @author Charles Pick
 * @package packages.reviews.components
 */
class AReviewable extends CActiveRecordBehavior implements IAReviewable {
	/**
	 * Whether the current user has reviewed this item or not
	 * @see getUserHasReviewed()
	 * @var boolean
	 */
	protected $_userHasReviewed;
	/**
	 * The review for this item by the current user if any.
	 * @see getUserReview()
	 * @var AReview
	 */
	protected $_userReview;

	/**
	 * Holds the total number of reviews for this item.
	 * @see getTotalReviews()
	 * @var integer
	 */
	protected $_totalReviews;
	
	/**
	 * Holds the total review score for this item
	 * @see getTotalReviewScore
	 * @var integer
	 */
	protected $_totalReviewScore;
	/**
	 * Gets the id of the object being reviewed.
	 * @return integer the id of the object being reviewed.
	 */
	public function getId() {
		return $this->owner->primaryKey;
	}
	
	/**
	 * Gets the name of the class that is being reviewed.
	 * @return string the owner model class name
	 */
	public function getClassName() {
		return get_class($this->owner);
	}
		
	
	/**
	 * Gets the data provider for this set of reviews
	 * @return CActiveDataProvider The dataProvider that retrieves the reviews
	 */
	public function getReviewDataProvider() {
		$dataProvider = new CActiveDataProvider(
				AReview::model()->
					ownedBy($this->owner)->
					with(array(
						"moderationItem"
					)));
		return $dataProvider;
	}
	
	/**
	 * Adds a review to the owner model
	 * @param Review $review The review to add
	 * @param boolean $runValidation Whether to run validation or not
	 * @return boolean Whether the save succeeded or not
	 */
	public function addReview(AReview $review, $runValidation = true) {
		$this->_userHasReviewed = null;
		$review->ownerModel = $this->getClassName();
		$review->ownerId = $this->getId();
		return $review->save($runValidation);
	}
	
	
	
	/**
	 * Named Scope: Orders a list of models by the most highly reviewd first
	 * @return CActiveRecord The owner object with the scope applied
	 */
	public function mostPopular() {
		$id = $this->owner->tableSchema->primaryKey;
		$reviewTable = Yii::app()->getModule('reviews')->reviewTable;
		$id = $this->owner->tableSchema->primaryKey;
		$criteria = new CDbCriteria;
		$criteria->select = "t.*, SUM($reviewTable.score) AS reviewScore";
		$criteria->join = "LEFT JOIN $reviewTable ON $reviewTable.ownerModel = :reviewOwnerModel AND $reviewTable.ownerId = t.$id";
		$criteria->group = "t.".$id;
		$criteria->params = array(
				":reviewOwnerModel" => $this->getClassName(),
			);
		$criteria->order = "reviewScore DESC";
		$this->owner->getDbCriteria()->mergeWith($criteria);
		return $this->owner;
	}
	
	/**
	 * Named Scope: Orders a list of models by the least popular first
	 * @return CActiveRecord The owner object with the scope applied
	 */
	public function leastPopular() {
		$id = $this->owner->tableSchema->primaryKey;
		$reviewTable = Yii::app()->getModule('reviews')->reviewTable;
		$criteria = new CDbCriteria;
		$criteria->select = "t.*, SUM($reviewTable.score) AS reviewScore";
		$criteria->join = "LEFT JOIN $reviewTable ON $reviewTable.ownerModel = :reviewOwnerModel AND $reviewTable.ownerId = t.$id";
		$criteria->group = "t.".$id;
		$criteria->params = array(
				":reviewOwnerModel" => $this->getClassName(),
			);
		$criteria->order = "reviewScore ASC";
		$this->owner->getDbCriteria()->mergeWith($criteria);
		return $this->owner;
	}
	
	/**
	 * Gets the configuration for a STAT relation with the total number of reviews for this model.
	 * This should be added to the relations() definition in the owner model.
	 * <pre>
	 * "totalReviews" => AReviewable::totalReviewsRelation(__CLASS__)
	 * </pre>
	 * @param string $className the name of the class
	 * @return array the relation configuration
	 */
	public static function totalReviewsRelation($className) {
		return array(
				CActiveRecord::STAT,
				"AReview",
				"ownerId",
				"condition" => "ownerModel = :reviewOwnerModel",
				"params" => array(
					":reviewOwnerModel" => $className
				)
			);
	}
	
	/**
	 * Gets the configuration for a STAT relation with the total review score for this model.
	 * This should be added to the relations() definition in the owner model.
	 * <pre>
	 * "totalReviewScore" => AReviewable::totalReviewScoreRelation(__CLASS__)
	 * </pre>
	 * @param string $className the name of the class
	 * @return array the relation configuration
	 */
	public static function totalReviewScoreRelation($className) {
		return array(
				CActiveRecord::STAT,
				"AReview",
				"ownerId",
				"select" => "IFNULL(SUM(score), 0)",
				"condition" => "ownerModel = :reviewOwnerModel",
				"params" => array(
					":reviewOwnerModel" => $className
				)
			);
	}
	
	/**
	 * Gets the configuration for a STAT relation with the average review score for this model.
	 * This should be added to the relations() definition in the owner model.
	 * <pre>
	 * "averageReviewScore" => AReviewable::averageReviewScoreRelation(__CLASS__)
	 * </pre>
	 * @param string $className the name of the class
	 * @return array the relation configuration
	 */
	public static function averageReviewScoreRelation($className) {
		return array(
				CActiveRecord::STAT,
				"AReview",
				"ownerId",
				"select" => "(IFNULL(SUM(score), 0)) / COUNT(*)",
				"condition" => "ownerModel = :reviewOwnerModel",
				"params" => array(
					":reviewOwnerModel" => $className
				)
			);
	}
	
	/**
	 * Gets the configuration for a STAT relation with whether the current user has reviewed this model
	 * This should be added to the relations() definition in the owner model.
	 * <pre>
	 * "userHasReviewed" => AReviewable::userHasReviewedRelation(__CLASS__)
	 * </pre>
	 * @param string $className the name of the class
	 * @return array the relation configuration
	 */
	public static function userHasReviewedRelation($className) {
		$relation =  array(
				CActiveRecord::STAT,
				"AReview",
				"ownerId",
				"condition" => "ownerModel = :reviewOwnerModel",
				"params" => array(
					":reviewOwnerModel" => $className
				)
			);
		if (Yii::app()->getModule('reviews')->requiresLogin) {
			$relation['condition'] .= " AND userHasReviewed.reviewerId = :reviewerId";
			$relation['params'][":reviewerId"] = Yii::app()->user->id;
		}
		else {
			$relation['condition'] .= " AND userHasReviewed.reviewerIP = :reviewerIP AND userHasReviewed.reviewerUserAgent = :reviewerUserAgent";
			$relation['params'][":reviewerIP"] = $_SERVER['REMOTE_ADDR'];
			$relation['params'][":reviewerUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
		}
		return $relation;
	}
	
	/**
	 * Gets the configuration for a HAS_ONE relation which returns the review for this item by the current user
	 * This should be added to the relations() definition in the owner model.
	 * <pre>
	 * "userReview" => AReviewable::userReviewRelation(__CLASS__)
	 * </pre>
	 * @param string $className the name of the class
	 * @return array the relation configuration
	 */
	public static function userReviewRelation($className) {
		$relation =  array(
				CActiveRecord::HAS_ONE,
				"AReview",
				"ownerId",
				"condition" => "ownerModel = :reviewOwnerModel",
				"params" => array(
					":reviewOwnerModel" => $className
				)
			);
		if (Yii::app()->getModule('reviews')->requiresLogin) {
			$relation['condition'] .= " AND userReview.reviewerId = :reviewerId";
			$relation['params'][":reviewerId"] = Yii::app()->user->id;
		}
		else {
			$relation['condition'] .= " AND userReview.reviewerIP = :reviewerIP AND userReview.reviewerUserAgent = :reviewerUserAgent";
			$relation['params'][":reviewerIP"] = $_SERVER['REMOTE_ADDR'];
			$relation['params'][":reviewerUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
		}
		return $relation;
	}
	
	/**
	 * Gets the configuration for a HAS_MANY relation which returns the reviews for this item
	 * This should be added to the relations() definition in the owner model.
	 * <pre>
	 * "reviews" => AReviewable::reviewsRelation(__CLASS__)
	 * </pre>
	 * @param string $className the name of the class
	 * @return array the relation configuration
	 */
	public static function reviewsRelation($className) {
		$relation =  array(
				CActiveRecord::HAS_MANY,
				"AReview",
				"ownerId",
				"condition" => "reviews.ownerModel = :reviewOwnerModel",
				"params" => array(
					":reviewOwnerModel" => $className
				)
			);
		return $relation;
	}
	/**
	 * Provides easy drop in relations for reviewable models.
	 * Usage:
	 * <pre>
	 * public function relations() {
	 * 	return CMap::mergeArray(AReviewable::relations(__CLASS__),array(
	 * 		"someRelation" => array(self::HAS_MANY,"blah","something")
	 * 	));
	 * }
	 * </pre>
	 * @param string $className the name of the class
	 * @return array The relations provided by this behavior
	 */
	public static function relations($className) {
		return array(
			"totalReviews" => self::totalReviewsRelation($className),
			"totalReviewScore" => self::totalReviewScoreRelation($className),
			"averageRating" => self::averageReviewScoreRelation($className),
			"userHasReviewed" => self::userHasReviewedRelation($className),
			"userReview" => self::userReviewRelation($className),
			"reviews" => self::reviewsRelation($className),
		);
	}
}
