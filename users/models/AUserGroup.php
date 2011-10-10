<?php
/**
 * Represents a group of users.
 * Users can belong to many groups. This can be used as a base for implementing social features
 * similar to facebook's groups or twitter's follow feature.
 *
 *
 * @author Charles Pick
 * @package packages.users.models
 *
 * @property integer $id The group ID
 * @property string $name The group name
 * @property AUser[] $users The users who belong to this group
 * @property AUser[] $admins The administrators for this group
 * @property integer $totalUsers The total number of users who belong to this group
 */

class AUserGroup extends CActiveRecord {
	/**
	 * Gets the model instance
	 * @param string $className the name of the class to instantiate
	 * @return AUserGroup
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	/**
	 * Gets the table name to use to store groups in
	 * @return string the table name
	 */
	public function tableName() {
		return "usergroups";
	}
	/**
	 * Gets the validation rules
	 * @return array the validation rules
	 */
	public function rules() {
		return array(
			array("name","required")
		);
	}
	/**
	 * Gets the relation configuration for this model
	 * @return array the relation configuration for this model
	 */
	public function relations() {
		$module = Yii::app()->getModule("users");
		$memberClassName = $module->userGroupMemberModelClass;
		$tableName = $memberClassName::model()->tableName();
		return array(
			"users" => array(self::MANY_MANY,$module->userModelClass,$tableName."(groupId,userId)"),
			"admins" => array(self::MANY_MANY,$module->userModelClass,$tableName."(groupId,userId)","condition" => "isAdmin = 1"),
			"totalUsers" => array(self::STAT,$module->userModelClass,$tableName."(groupId,userId)"),
		);
	}
	/**
	 * Gets the data provider for members of this group
	 * @return CActiveDataProvider the data provider
	 */
	public function getMemberDataProvider() {
		$module = Yii::app()->getModule("users"); /* @var AUsersModule $module */
		$userClassName = $module->userModelClass;
		$memberClassName = $module->userGroupMemberModelClass;
		$memberTable = $memberClassName::model()->tableName();
		$user = $userClassName::model(); /* @var AUser $user */
		$criteria = new CDbCriteria();
		$criteria->join = "INNER JOIN ".$memberTable." ON ".$memberTable.".userId = ".$user->getTableAlias().".id AND ".$memberTable.".groupId = :groupId";
		$criteria->params = array(":groupId" => $this->id);
		return new CActiveDataProvider($user,
										array(
											"criteria" => $criteria,
										));
	}

	/**
	 * Adds a user to the group
	 * @param AUser $user the user to add
	 * @param boolean $isAdmin whether the user is an administrator of this group or not
	 * @return boolean true if the user was added to the group, otherwise false
	 */
	public function addUser(AUser $user, $isAdmin = false) {
		try {
			$module = Yii::app()->getModule("users");
			$className = $module->userGroupMemberModelClass;
			$member = new $className;
			$member->groupId = $this->id;
			$member->userId = $user->id;
			$member->isAdmin = $isAdmin;
			return $member->save();
		}
		catch (CDbException $e) {
			return false;
		}
	}
	/**
	 * Removes a user from the group.
	 * If AUsersModule::$deleteEmptyUserGroups is set to true, the group will also be deleted if it is now empty
	 * @param AUser $user the user to add
	 * @return boolean whether the user was removed or not
	 */
	public function removeUser(AUser $user) {
		$module = Yii::app()->getModule("users");
		$className = $module->userGroupMemberModelClass;
		$attributes = array("userId" => $user->id, "groupId" => $this->id);
		$member = $className::model()->findByAttributes($attributes);
		if (!is_object($member)) {
			return false;
		}
		$result = $member->delete();
		if (!$result) {
			return false;
		}
		if (!$module->deleteEmptyUserGroups) {
			return true;
		}
		if ($className::model()->countByAttributes($attributes) == 0) {
			// delete this group because it is empty
			return $this->delete();
		}
		return true;
	}
	/**
	 * Determines whether a user is in the given group or not
	 * @param AUser|integer $user the user object or primary key
	 * @return boolean true if the user is a member of this group
	 */
	public function hasUser(AUser $user) {
		if ($user instanceof AUser) {
			$user = $user->id;
		}
		$module = Yii::app()->getModule("users");
		$className = $module->userGroupMemberModelClass;
		return (bool) $className::model()->countByAttributes(
			array(
				"userId" => $user,
				"groupId" => $this->id
			)
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}