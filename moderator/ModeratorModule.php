<?php
Yii::import("blocks.moderator.interfaces.*");
Yii::import("blocks.moderator.models.*");
Yii::import("blocks.moderator.components.*");
/**
 * Holds functionality relating to moderation of user submitted content
 * @package blocks.moderator
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
