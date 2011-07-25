<?php
Yii::import("packages.moderator.interfaces.*");
Yii::import("packages.moderator.models.*");
Yii::import("packages.moderator.components.*");
/**
 * Holds functionality relating to moderation of user submitted content
 * @package packages.moderator
 * @author Charles Pick
 */
class ModeratorModule extends CWebModule {
	
	/**
	 * The name of the table that moderation information should be stored in.
	 * Defaults to "moderation".
	 * @var string
	 */
	public $moderationTable = "moderation";
	
	/**
	 * Called when the module is being created.
	 * Put any module specific configuration here
	 */
	public function init()
	{
		return parent::init();
	}

	
}
