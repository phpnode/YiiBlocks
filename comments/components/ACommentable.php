<?php
/**
 * ACommentable allows comments to be attached to models
 * @author Charles Pick
 * @package packages.comments.components
 */
class ACommentable extends CActiveRecordBehavior {
	/**
	 * Holds the comments
	 * @var AComment[]
	 */
	protected $_comments;


	/**
	 * Gets all the comments for this model, both approved and unapproved
	 * @return AComment[] an array of comments
	 */
	public function getComments() {
		if ($this->_comments === null) {
			$this->_comments = $this->comments();
		}
		return $this->_comments;
	}

	/**
	 * Gets the comment tree for this item.
	 * @return AComment[] The nested comments
	 */
	public function getCommentTree() {
		if ($this->_comments === null) {
			if (Yii::app()->getModule("comments")->useNestedSet) {
				$this->_comments = array();
				$criteria = new CDbCriteria;
				$criteria->order = "root, lft";

				$nodes = $this->comments($criteria);
				$stack = array();
				$l = 0;
				foreach($nodes as $node) {
					$node->replies = array();
					$l = count($stack);
					// are these different levels?

					while($l > 0 && $stack[$l - 1]->level >= $node->level) {
						array_pop($stack);
						$l--;
					}
					if ($l == 0) {
						// stack is empty
						$i = count($this->_comments);
						$this->_comments[$i] = $node;
						$stack[] =& $this->_comments[$i];
					}
					else {
						// add to parent
						$replies = $stack[$l - 1]->replies;
						$i = count($replies);
						$replies[$i] = $node;
						$stack[$l - 1]->replies = $replies;
						$stack[] = $replies[$i];
					}
				}

			}
			else {
				$this->_comments = $this->comments();
			}
		}
		return $this->_comments;
	}

	/**
	 * Gets the approved comments for this model
	 * @return AComment[] an array of comments
	 */
	public function getApprovedComments() {
		if ($this->_comments !== null) {
			$return = array();
			foreach($this->_comments as $comment) {
				if ($comment->isApproved) {
					$return[] = $comment;
				}
			}
			return $return;
		}
		return $this->comments(array("condition" => "approved = 1"));
	}

	/**
	 * Gets the unapproved comments for this model
	 * @return AComment[] an array of comments
	 */
	public function getUnapprovedComments() {
		if ($this->_comments !== null) {
			$return = array();
			foreach($this->_comments as $comment) {
				if (!$comment->isApproved) {
					$return[] = $comment;
				}
			}
			return $return;
		}
		return $this->comments(array("condition" => "approved = 0"));
	}

	/**
	 * Gets the comments for this model
	 * @param mixed $criteria The CDbCriteria or array used to restrict the results
	 * @param array $params The parameters to bind to the query
	 * @return AComment[] an array of comments
	 */
	public function comments($criteria = null, $params = null) {
		if (Yii::app()->getModule("comments")->votableComments) {
			return AComment::model()->ownedBy($this->owner)->findAll($criteria, $params);
		}
		else {
			return AComment::model()->ownedBy($this->owner)->findAll($criteria, $params);
		}
	}
	/**
	 * Gets the data provider for this set of comments
	 * @return CActiveDataProvider The dataProvider that retrieves the comments
	 */
	public function getCommentDataProvider() {
		if (Yii::app()->getModule("comments")->votableComments) {
			$comment = AComment::model()->ownedBy($this->owner)->mostPopular();
		}
		else {
			$comment = AComment::model()->ownedBy($this->owner);
		}

		$dataProvider = new CActiveDataProvider($comment);
		return $dataProvider;
	}

	/**
	 * Adds a comment to the owner model
	 * @param AComment $comment The comment to add
	 * @param boolean $runValidation Whether to run validation or not
	 * @return boolean Whether the save succeeded or not
	 */
	public function addComment(AComment $comment, $runValidation = true) {

		$comment->ownerModel = get_class($this->owner);
		$comment->ownerId = $this->owner->primaryKey;
		if (Yii::app()->getModule("comments")->useNestedSet) {
			return $comment->saveNode($runValidation);
		}
		else {
			return $comment->save($runValidation);
		}
	}
}
