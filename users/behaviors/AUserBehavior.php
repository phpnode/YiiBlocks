<?php
/**
 * Provides some extra functionality for CWebUser.
 * This behavior should be attached to the application user component
 * to allow direct access to the model of the currently logged in user.
 * @author Charles Pick
 * @package packages.users.behaviors
 */
class AUserBehavior extends CBehavior {
	/**
	 * Holds the model for the currently logged in user
	 * @see getModel()
	 * @see setModel()
	 * @var AUser
	 */
	protected $_model;
	
	/**
	 * Gets the user model for the currently logged in user
	 * @return AUser the model for the logged in user, or false if the user is not logged in
	 */
	public function getModel() {
		if (Yii::app()->user->isGuest) {
			return false;
		}
		if ($this->_model === null) {
			$modelClass = Yii::app()->getModule("users")->userModelClass;
			$this->_model = $modelClass::model()->findByPk(Yii::app()->user->id);
		}
		return $this->_model;
	}
	/**
	 * Sets the model for the currently logged in user
	 * @param AUser $model the user model
	 * @return AUser $model the user model
	 */
	public function setModel(AUser $model) {
		return $this->_model = $model;
	}
}
