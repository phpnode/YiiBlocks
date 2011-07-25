<?php
Yii::import("packages.voting.interfaces.*");
Yii::import("packages.voting.models.*");
Yii::import("packages.voting.components.*");
/**
 * Holds functionality related to voting.
 * Votes can be attached to other models and are managed via the vote controller.
 * @package packages.voting
 * @author Charles Pick
 */
class VotingModule extends CWebModule {
	
	/**
	 * The name of the votes table
	 * Defaults to "votes"
	 * @var string 
	 */
	public $voteTable = "votes";
	
	/**
	 * Whether users must be logged in or not to vote, defaults to true.
	 * @var boolean
	 */
	public $requiresLogin = true;
	
	/**
	 * Holds an array of items to show in the main menu
	 */
	public $mainMenu = array(
			array('label'=>'Home', 'url'=>array('/reviews/review/admin'))
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
