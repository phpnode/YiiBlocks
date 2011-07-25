<?php
/**
 * Allows easy moderation of user submitted content.
 * This behavior can be attached to any active record.
 * @author Charles Pick
 * @package packages.moderator.components
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
		$relations = $this->owner->relations();
		if (isset($relations['moderationItem'])) {
			$this->_moderationItem = $this->owner->moderationItem;
		}
		else if ($this->_moderationItem === null) {
			$this->_moderationItem = AModerationItem::model()->ownedBy($this->owner)->find();
		}
		if (!is_object($this->_moderationItem)) {
			$this->_moderationItem = false;
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
	 * Gets the configuration for a HAS_MANY relation which returns the moderation item for this item
	 * This should be added to the relations() definition in the owner model.
	 * <pre>
	 * "moderationItem" => AModeratable::moderationItemRelation(__CLASS__)
	 * </pre>
	 * @param string $className the name of the class
	 * @return array the relation configuration
	 */
	public static function moderationItemRelation($className) {
		$relation =  array(
				CActiveRecord::HAS_ONE,
				"AModerationItem",
				"ownerId",
				"condition" => "moderationItem.ownerModel = :moderationOwnerId",
				"params" => array(
					":moderationOwnerId" => $className
				)
			);
		return $relation;
	}
	
	
	/**
	 * Provides easy drop in relations for moderatable models.
	 * Usage:
	 * <pre>
	 * public function relations() {
	 * 	return CMap::mergeArray(AModeratable::relations(__CLASS__),array(
	 * 		"someRelation" => array(self::HAS_MANY,"blah","something")
	 * 	));
	 * }
	 * </pre>
	 * @param string $className the name of the class
	 * @return array The relations provided by this behavior
	 */
	public static function relations($className) {
		return array(
			"moderationItem" => self::moderationItemRelation($className),
		);
	}
	/**
	 * Gets a list of named scopes that can be applied to the owner model
	 * Usage:
	 * <pre>
	 * public function scopes() {
	 * 	return CMap::mergeArray($this->asa("AModeratable")->scopes(),array(
	 * 		"anotherScope" => array(...)
	 * 	));
	 * }
	 * </pre>
	 * @param string $className the name of the class
	 * @return array The scopes provided by this behavior
	 */
	public static function scopes($className) {
		
		$tableName = AModerationItem::model()->tableName();
		$alias = "moderationStatus";
		return array(
			"pending" => array(
					"join" => "INNER JOIN $tableName AS $alias ON $alias.ownerModel = :moderationOwnerModel AND $alias.ownerId = t.id",
					"condition" => "$alias.status = :moderationStatus",
					"params" => array(
							":moderationOwnerModel" => $className,
							":moderationStatus" => IAModeratable::PENDING
						),
				),
			"approved" => array(
					"join" => "INNER JOIN $tableName AS $alias ON $alias.ownerModel = :moderationOwnerModel AND $alias.ownerId = t.id",
					"condition" => "$alias.status = :moderationStatus",
					"params" => array(
							":moderationOwnerModel" => $className,
							":moderationStatus" => IAModeratable::APPROVED
						),
				),
			"denied" => array(
					"join" => "INNER JOIN $tableName AS $alias ON $alias.ownerModel = :moderationOwnerModel AND $alias.ownerId = t.id",
					"condition" => "$alias.status = :moderationStatus",
					"params" => array(
							":moderationOwnerModel" => $className,
							":moderationStatus" => IAModeratable::DENIED
						),
				),
			);
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
		$criteria->join = "INNER JOIN $tableName ON $tableName.ownerModel = :moderationOwnerModel AND $tableName.ownerId = t.id";
		$criteria->addCondition("$tableName.status = :moderationStatus",$operator);
		$criteria->params = array(
				":moderationOwnerModel" => $this->getClassName(),
				":moderationStatus" => $status
			);
		$this->owner->getDbCriteria()->mergeWith($criteria);
		return $this->owner;
	}
}
