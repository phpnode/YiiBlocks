<?php
/**
 * Holds information about a member of a user group
 *
 * @author Charles Pick
 * @package packages.users.models
 *
 * @property integer $groupId The id of the group
 * @property integer $userId The id of the user
 * @property integer $timeAdded The time the user joined the group
 * @property boolean $isAdmin Whether this user is an administrator of the group or not
 */
class AUserGroupMember extends CActiveRecord {

	/**
	 * Gets the model instance
	 * @param string $className the name of the class to instantiate
	 * @return AUserGroupMember
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	/**
	 * Gets the table name to use to store groups in
	 * @return string the table name
	 */
	public function tableName() {
		return "usergroupmembers";
	}
	/**
	 * The before save event, sets the time joined value if necessary
	 * @return boolean whether the save should continue or not
	 */
	public function beforeSave() {
		if ($this->isNewRecord) {
			$this->timeJoined = (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
		}
		return parent::beforeSave();
	}

	/**
	 * Gets the relation configuration
	 * @return array the relation configuration
	 */
	public function relations() {
		$module = Yii::app()->getModule("users");
		return array(
			array(self::BELONGS_TO,$module->userModelClass,"userId"),
			array(self::BELONGS_TO,$module->userGroupModelClass,"groupId"),
		);
	}

}