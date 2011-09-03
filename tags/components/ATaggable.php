<?php
Yii::import("packages.tags.components.*");
/**
 * Allows models to be "taggable".
 * Tags can be added to models, and models can then be searched for by these tags.
 * @author Charles Pick
 * @package packages.tags.components
 */
class ATaggable extends CActiveRecordBehavior {
	/**
	 * An list of tags that belong to this object
	 * @var ATagList
	 */
	protected $_tags;
	/**
	 * A list of tags that
	 * @var ATagList
	 */
	protected $_userTags;

	/**
	 * Attaches the behavior object to the component.
	 * @param CActiveRecord $owner the component that this behavior is to be attached to.
	 */
	public function attach($owner) {
		parent::attach($owner);
		$module = Yii::app()->getModule("tags");
		$owner->getMetaData()->addRelation(
											"tagAssignments",
											array(
												CActiveRecord::HAS_MANY,
												$module->tagAssignmentModelClass,
												"ownerId",
												"condition" => "tagAssignments.ownerModel = :tagOwnerModel",
												"params" => array(":tagOwnerModel" => get_class($owner)),
											));
	}
	/**
	 * Saves the changes to the tag list
	 * @param CModelEvent $event
	 */
	public function afterSave($event) {
		if (is_object($this->_tags)) {
			$this->saveTagChanges();
		}
		if (is_object($this->_userTags)) {
			$this->saveUserTagChanges();
		}
	}

	/**
	 * Saves the changes to the tag list
	 */
	protected  function saveTagChanges() {
		$assignmentModelClass = Yii::app()->getModule("tags")->tagAssignmentModelClass;
		if (count($this->_tags->added)) {
			foreach($this->_tags->added as $tagId) {
				$model = new $assignmentModelClass;
				$model->ownerModel = get_class($this->owner);
				$model->ownerId = $this->owner->primaryKey;
				$model->tagId = $tagId;
				$model->userId = (!isset(Yii::app()->user) || Yii::app()->user->isGuest ? null : Yii::app()->user->id);
				$model->save();
			}
		}
		if (count($this->_tags->removed)) {
			$criteria = new CDbCriteria;
			$criteria->condition = "ownerModel = :ownerModel AND ownerId = :ownerId";
			$criteria->params = array(
				":ownerModel" => get_class($this->owner),
				":ownerId" => $this->owner->primaryKey,
			);
			$criteria->addInCondition("tagId",$this->_tags->removed);

			foreach($assignmentModelClass::model()->findAll($criteria) as $tagAssignment) {
				$tagAssignment->delete();
			}
		}
	}

	/**
	 * Saves the changes to the user's tag list
	 */
	protected  function saveUserTagChanges() {
		$assignmentModelClass = Yii::app()->getModule("tags")->tagAssignmentModelClass;
		if (count($this->_userTags->added)) {
			foreach($this->_userTags->added as $tagId) {
				$model = new $assignmentModelClass;
				$model->ownerModel = get_class($this->owner);
				$model->ownerId = $this->owner->primaryKey;
				$model->tagId = $tagId;
				$model->userId = Yii::app()->user->id;
				$model->save();
			}
		}
		if (count($this->_userTags->removed)) {
			$criteria = new CDbCriteria;
			$criteria->condition = "ownerModel = :ownerModel AND ownerId = :ownerId AND userId = :userId";
			$criteria->params = array(
				":ownerModel" => get_class($this->owner),
				":ownerId" => $this->owner->primaryKey,
				":userId" => Yii::app()->user->id,
			);
			$criteria->addInCondition("tagId",$this->_userTags->removed);
			foreach($assignmentModelClass::model()->findAll($criteria) as $tagAssignment) {
				$tagAssignment->delete();
			}
		}
	}
	/**
	 * Gets a list of tags that belong to this model, with their scores
	 * @return ATagList the list of tags that belong to this model
	 */
	public function getTags() {
		if ($this->_tags === null) {
			$this->_tags = new ATagList();
			if (!$this->owner->isNewRecord) {
				$criteria = new CDbCriteria;
				$module = Yii::app()->getModule("tags");
				$tagTable = $module->tagTableName;
				$criteria->select = "tag.*, COUNT(t.userId) AS score";
				$criteria->join = "INNER JOIN $tagTable tag ON tag.id = t.tagId";
				$criteria->condition = "t.ownerModel = :ownerModel AND t.ownerId = :ownerId";
				$criteria->params[":ownerModel"] = get_class($this->owner);
				$criteria->params[":ownerId"] = $this->owner->primaryKey;
				$criteria->group = "t.tagId";
				$command = Yii::app()->db->getCommandBuilder()->createFindCommand($module->tagAssignmentTableName,$criteria);
				$tagModelClass = $module->tagModelClass;
				foreach($tagModelClass::model()->populateRecords($command->queryAll()) as $tag) {
					$this->_tags->add($tag);
				}
			}
		}
		return $this->_tags;
	}
	/**
	 * Sets the tags that have been assigned to this model
	 * @param mixed $value either an array, a comma separated string or an instance of ATagList
	 * @return ATagList the list of tags that have been assigned to this model
	 */
	public function setTags($value) {
		$tagList = $this->getTags();
		return $tagList->fromString($value);
	}
	/**
	 * Gets a list of tags that the current user has assigned to this model
	 * @return ATagList the list of tags that the current user has assigned to this model
	 */
	public function getUserTags() {
		if ($this->_userTags === null) {
			$this->_userTags = new ATagList();
			if (!$this->owner->isNewRecord) {
				$criteria = new CDbCriteria;
				$module = Yii::app()->getModule("tags");
				$tagTable = $module->tagTableName;
				$criteria->join = "INNER JOIN $tagTable tag ON tag.id = t.tagId";
				$criteria->condition = "t.ownerModel = :ownerModel AND t.ownerId = :ownerId AND t.userId = :userId";
				$criteria->params[":ownerModel"] = get_class($this->owner);
				$criteria->params[":ownerId"] = $this->owner->primaryKey;
				$criteria->params[":userId"] = Yii::app()->user->id;
				$criteria->group = "t.tagId";
				$tagModelClass = $module->tagModelClass;
				foreach($tagModelClass::model()->findAll($criteria) as $tag) {
					$this->_userTags->add($tag);
				}
			}
		}
		return $this->_userTags;
	}
	/**
	 * Sets the tags that the currently logged in user has assigned to this model
	 * @param mixed $value either an array, a comma separated string or an instance of ATagList
	 * @return ATagList the list of tags that have been assigned to this model by this user
	 */
	public function setUserTags($value) {
		$tagList = $this->getUserTags();
		return $tagList->fromString($value);
	}
	/**
	 * Adds a tag to this model
	 * @param string|ATag $tag either a string representing a tag or an instance of ATag
	 * @return boolean whether the tag was added or not
	 */
	public function addTag($tag) {
		if (is_string($tag)) {
			$modelClass = Yii::app()->getModule("tags")->tagModelClass;
			$t = $tag;
			$tag = $modelClass::model()->findByAttributes(array("tag" => $t));
			if (!is_object($tag)) {
				$tag = new $modelClass;
				$tag->tag = $t;
				$tag->save();
			}
		}
		$assignmentClass = Yii::app()->getModule("tags")->tagAssignmentModelClass;
		$attributes = array(
			"ownerModel" => get_class($this->owner),
			"ownerId" => $this->owner->primaryKey,
			"tagId" => $tag->id,
			"userId" => Yii::app()->user->id,
		);

		$assignment = $assignmentClass::model()->findByAttributes($attributes);
		if (is_object($assignment)) {
			return false;
		}
		$assignment = new $assignmentClass;
		foreach($attributes as $attribute => $value) {
			$assignment->{$attribute} = $value;
		}
		return $assignment->save();

	}
	/**
	 * Removes a tag from this model
	 * @param string|ATag $tag either a string representing a tag or an instance of ATag
	 * @return boolean whether the tag was removed or not
	 */
	public function removeTag($tag) {
		if (is_string($tag)) {
			$modelClass = Yii::app()->getModule("tags")->tagModelClass;
			$t = $tag;
			$tag = $modelClass::model()->findByAttributes(array("tag" => $t));
			if (!is_object($tag)) {
				return false;
			}
		}
		$assignmentClass = Yii::app()->getModule("tags")->tagAssignmentModelClass;
		$attributes = array(
			"ownerModel" => get_class($this->owner),
			"ownerId" => $this->owner->primaryKey,
			"tagId" => $tag->id,
			"userId" => Yii::app()->user->id,
		);

		$assignment = $assignmentClass::model()->findByAttributes($attributes);
		if (!is_object($assignment)) {
			return false;
		}
		return $assignment->delete();
	}
}