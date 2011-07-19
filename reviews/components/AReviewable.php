<?php
Yii::import("blocks.reviews.interfaces.IAReviewable");
/**
 * Allows models to be reviewed by users
 * @author Charles Pick
 * @package blocks.reviews.components
 */
class AReviewable extends CActiveRecordBehavior implements IAReviewable {
	/**
	 * Whether the current user has reviewed this item or not
	 * @see getUserHasReviewed()
	 * @var boolean
	 */
	protected $_userHasReviewed;

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
	 * Determine whether the current user has reviewed this item or not. 
	 * @return boolean whether the current use has reviewd or not
	 */
	public function getUserHasReviewed() {
		
		if ($this->_userHasReviewed === null) {
			$criteria = new CDbCriteria;
			if (Yii::app()->getModule('reviews')->requiresLogin) {
				$criteria->addCondition("reviewerId = :reviewerId");
				$criteria->params[":reviewerId"] = Yii::app()->user->id;
			}
			else {
				$criteria->addCondition("reviewerIP = :reviewerIP AND reviewerUserAgent = :reviewerUserAgent");
				$criteria->params[":reviewerIP"] = $_SERVER['REMOTE_ADDR'];
				$criteria->params[":reviewerUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
			}
			$review = AReview::model()->ownedBy($this->owner)->find($criteria);
			if (is_object($review)) {
				$this->_userHasReviewed = true;
			}
			else {
				$this->_userHasReviewed = false;
			}
		}
		return $this->_userHasReviewed;
	}
	/**
	 * Sets whether the user has reviewed or not.
	 * This is mainly used internally, it does not modify anything permanently.
	 * @param boolean $value Whether the user has reviewed or not
	 */
	public function setUserHasReviewed($value) {
		return $this->_userHasReviewed = (bool) $value;
	}
	
		
	/**
	 * Gets the total number of reviews for this item
	 * @return integer The total number of reviews for this item
	 */
	public function getTotalReviews() {
		if (property_exists($this->owner,"_totalReviewScore")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		if ($object->_totalReviews === null) {
			$reviewTable = Yii::app()->getModule('reviews')->reviewTable;
			$id = $this->owner->tableSchema->primaryKey;
			$sql = "SELECT COUNT($reviewTable.id) AS _totalReviews, IFNULL(SUM($reviewTable.score), 0) as _totalReviewScore FROM $reviewTable WHERE $reviewTable.ownerModel = :reviewOwnerModel AND $reviewTable.ownerId = :reviewOwnerId";
			$cmd = Yii::app()->db->createCommand($sql);
			$cmd->bindValues(array(
				":reviewOwnerModel" => $this->getClassName(),
				":reviewOwnerId" => $this->getId(),
			));
			$row = $cmd->queryRow();
			$object->_totalReviewScore = $row['_totalReviewScore'];
			$object->_totalReviews = $row['_totalReviews'];
		}	
		return (int) $object->_totalReviews;	
	}
	
	/**
	 * Gets the total score for this reviewable item.
	 * @return integer The score
	 */
	public function getTotalReviewScore() {
		if (property_exists($this->owner,"_totalReviewScore")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		if ($object->_totalReviewScore === null) {
			$voteTable = Yii::app()->getModule('reviews')->reviewTable;
			$id = $this->owner->tableSchema->primaryKey;
			$sql = "SELECT COUNT($reviewTable.id) AS _totalReviews, IFNULL(SUM($reviewTable.score), 0) as _totalReviewScore FROM $reviewTable WHERE $reviewTable.ownerModel = :reviewOwnerModel AND $reviewTable.ownerId = :reviewOwnerId";
			$cmd = Yii::app()->db->createCommand($sql);
			$cmd->bindValues(array(
				":reviewOwnerModel" => $this->getClassName(),
				":reviewOwnerId" => $this->getId(),
			));
			$row = $cmd->queryRow();
			$object->_totalReviewScore = $row['_totalReviewScore'];
			$object->_totalReviews = $row['_totalReviews'];
		}	
		return (int) $object->_totalReviewScore;	
	}
	
	/**
	 * Gets the average rating for this model
	 * @return float the average rating for this model
	 */
	public function getAverageRating() {
		$totalReviews = $this->getTotalReviews();
		$totalReviewScore = $this->getTotalReviewScore();
		return $totalReviewScore / $totalReviews;
	}
	
	/**
	 * Gets the data provider for this set of reviews
	 * @return CActiveDataProvider The dataProvider that retrieves the reviews
	 */
	public function getReviewDataProvider() {
		$dataProvider = new CActiveDataProvider(AReview::model()->ownedBy($this->owner));
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
}
