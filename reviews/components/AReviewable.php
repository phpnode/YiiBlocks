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
						"moderationItem",
						"totalVoteScore",
						"totalUpvotes",
						"totalDownvotes",
						"userHasVoted",
					))->together());
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
	 * Attaches the behavior to the model
	 * @param CComponent $component The model to attach to
	 */
	public function attach($component) {
		parent::attach($component);
		$this->owner->metaData->addRelation(
			"totalReviews",
			array(
				CActiveRecord::STAT,
				"AReview",
				"ownerId",
				"condition" => "ownerModel = :reviewOwnerModel",
				"params" => array(
					":reviewOwnerModel" => $this->getClassName()
				),
				"group" => false,
			)
		);
		
		$this->owner->metaData->addRelation(
			"totalReviewScore",
			array(
				CActiveRecord::STAT,
				"AReview",
				"ownerId",
				"select" => "IFNULL(SUM(score), 0)",
				"condition" => "ownerModel = :reviewOwnerModel",
				"params" => array(
					":reviewOwnerModel" => $this->getClassName()
				),
				"group" => false,
			)
		);
		
		$this->owner->metaData->addRelation(
			"averageRating",
			array(
				CActiveRecord::STAT,
				"AReview",
				"ownerId",
				"select" => "(IFNULL(SUM(score), 0)) / COUNT(*)",
				"condition" => "ownerModel = :reviewOwnerModel",
				"params" => array(
					":reviewOwnerModel" => $this->getClassName()
				),
				"group" => false,
			)
		);
		
		$relation =  array(
				CActiveRecord::STAT,
				"AReview",
				"ownerId",
				"condition" => "ownerModel = :reviewOwnerModel",
				"params" => array(
					":reviewOwnerModel" => $this->getClassName()
				),
				"group" => false,
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
		
		
		$this->owner->metaData->addRelation(
			"userHasReviewed",
			$relation
		);
		
		
		$relation =  array(
				CActiveRecord::HAS_ONE,
				"AReview",
				"ownerId",
				"condition" => "ownerModel = :reviewOwnerModel",
				"params" => array(
					":reviewOwnerModel" => $this->getClassName()
				),
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
		$this->owner->metaData->addRelation(
			"userReview",
			$relation
		);
		
		$this->owner->metaData->addRelation(
			"reviews",
			array(
				CActiveRecord::HAS_MANY,
				"AReview",
				"ownerId",
				"condition" => "reviews.ownerModel = :reviewOwnerModel",
				"params" => array(
					":reviewOwnerModel" => $this->getClassName()
				),
			)
		);
		
	}
	

}
