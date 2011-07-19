<?php
/**
 * Allows easy moderation of user submitted content.
 * This behavior can be attached to any active record.
 * @author Charles Pick
 * @package blocks.moderator.components
 */
class AModeratable extends CActiveRecordBehavior implements IAModeratable {
	/**
	 * Holds the moderation item for this model
	 * @see getModerationItem
	 * @see setModerationItem
	 * @var AModerationItem
	 */
	protected $_moderationItem;
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
	 * Whether this particular object should be moderated.
	 * The default implementation returns true if the owner model is new, otherwise false.
	 * @return boolean true if the object should be moderated
	 */
	public function isModeratable() {
		return $this->owner->isNewRecord;
	}
	
	/**
	 * Determine whether this model has been moderated or not.
	 * @return boolean true if the item has been moderated
	 */
	public function getIsModerated() {
		return $this->getModerationItem() !== false;
	}
	/**
	 * Determine whether this model has been approved or not.
	 * @return boolean true if the model has been approved.
	 */
	public function getIsApproved() {
		if ($this->getModerationItem() === false) {
			return false;
		}
		return $this->getModerationItem()->status === IAModeratable::APPROVED;
	}
	
	/**
	 * Determine whether this model has been denied or not.
	 * @return boolean true if the model has been denied.
	 */
	public function getIsDenied() {
		if ($this->getModerationItem() === false) {
			return false;
		}
		return $this->getModerationItem()->status === IAModeratable::DENIED;
	}
	
	/**
	 * Determine whether this model is pending moderation or not
	 * @return boolean true if the model is still pending moderation.
	 */
	public function getIsPending() {
		if ($this->getModerationItem() === false) {
			return false;
		}
		return $this->getModerationItem()->status === IAModeratable::PENDING;
	}
	
	/**
	 * Approves this model.
	 * @see AModerationItem::approve()
	 * @return boolean Whether the approval succeeded
	 */
	public function approve() {
		return $this->getModerationItem()->approve();
	}
	
	/**
	 * Denies this model.
	 * @see AModerationItem::approve()
	 * @return boolean Whether the approval succeeded
	 */
	public function deny() {
		return $this->getModerationItem()->deny();
	}
	
	/**
	 * The after save event. Adds a moderation item to the db if required.
	 * @param CEvent $event The raised event
	 */
	public function afterSave($event) {
		if ($event->sender->isNewRecord) {
			$model = new AModerationItem;
			$model->ownerModel = $this->getClassName();
			$model->ownerId = $this->getId();
			$model->save();
			$this->_moderationItem = $model;
		}
	}
	
	/**
	 * The after delete event. Deletes a moderation item to the db if required.
	 * @param CEvent $event The raised event
	 */
	public function afterDelete($event) {
		$model = $this->getModerationItem();
		if (is_object($model)) {
			$model->delete();
			$this->_moderationItem = false;
		}
	}
	
	
	
	/**
	 * Gets the moderation item for this model
	 * @return AModerationItem the moderation item for this model, or false if none exists
	 */
	public function getModerationItem() {
		if ($this->_moderationItem === null) {
		
			$this->_moderationItem = AModerationItem::model()->ownedBy($this->owner)->find();
			if (!is_object($this->_moderationItem)) {
				$this->_moderationItem = false;
			}
		}
		return $this->_moderationItem;
	}
	/**
	 * Sets the moderation item for this model
	 * @param AModerationItem $value The moderation item for this model
	 * @return AModerationItem the value
	 */
	public function setModerationItem(AModerationItem $value) {
		return $this->_moderationItem = $value;
	}
	
	/**
	 * Named Scope: Find models by their moderation status.
	 * @param string $status The moderation status, either IAModeratable::PENDING, IAModeratable::APPROVED or IAModeratable::DENIED
	 * @param string $operator The operator to use when adding the condition, defaults to "AND"
	 * @return CActiveRecord $this->owner with the scope applied
	 */
	public function moderated($status = self::APPROVED, $operator = "AND") {
		$criteria = new CDbCriteria;
		$tableName = AModerationItem::model()->tableName();
		$criteria->join = "INNER JOIN $tableName ON $tableName.ownerModel = :ownerModel AND $tableName.ownerId = t.id";
		$criteria->addCondition("$tableName.status = :moderationStatus",$operator);
		$criteria->params = array(
				":ownerModel" => $this->getClassName(),
				":moderationStatus" => $status
			);
		$this->owner->getDbCriteria()->mergeWith($criteria);
		return $this->owner;
	}
}
