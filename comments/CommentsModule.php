<?php
Yii::import("packages.comments.interfaces.*");
Yii::import("packages.comments.models.*");
Yii::import("packages.comments.components.*");
/**
 * Holds functionality related to comments.
 * Comments can be attached to other models and are managed via the comment controller.
 * @package packages.reviews
 * @author Charles Pick
 */
class CommentsModule extends CWebModule {
	
	/**
	 * The name of the comments table
	 * Defaults to "comments"
	 * @var string 
	 */
	public $commentTable = "comments";
	
	/**
	 * Whether users must be logged in or not to comment, defaults to true.
	 * @var boolean
	 */
	public $requiresLogin = true;
	
	/**
	 * Holds an array of items to show in the main menu
	 */
	public $mainMenu = array(
			array('label'=>'Home', 'url'=>array('/comments/comment/admin'))
		);
		
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
