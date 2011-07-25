<?php
Yii::import("packages.ypm.components.*");
Yii::import("packages.ypm.models.*");
Yii::import("packages.ypm.exceptions.*");
/**
 * Holds Yii Package Manager functionality
 * @package packages.ypm
 * @author Charles Pick
 */
class YpmModule extends CWebModule {
	
	
		
	/**
	 * Called when the module is being created.
	 * Put any module specific configuration here
	 */
	public function init()
	{
		return parent::init();
	}
	/**
	 * This function is called before the controller action is run
	 * @param CController $controller The controller to run
	 * @param CAction $action The action to run on the controller
	 * @return boolean True if the action should be executed, false if the action should be stopped
	 */
	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
	
}
