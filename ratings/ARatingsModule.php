<?php
Yii::import("packages.ratings.models.*");
Yii::import("packages.ratings.interfaces.*");
Yii::import("packages.ratings.components.*");
/**
 * Holds functionality relating to user supplied ratings.
 * Ratings can be attached to other models and are managed via the rating controller.
 * @package packages.ratings
 * @author Charles Pick
 */
class ARatingsModule extends CWebModule {

	/**
	 * The name of the ratings table
	 * Defaults to "ratings"
	 * @type string
	 */
	public $ratingTable = "ratings";

	/**
	 * Whether users must be logged in or not to rate items, defaults to true.
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
