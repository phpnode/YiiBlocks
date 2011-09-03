<?php
Yii::import("packages.tags.components.*");
Yii::import("packages.tags.models.*");
/**
 * Deals with managing and assigning tags to taggable models
 * @author Charles Pick
 * @package packages.tags
 */
class ATagsModule extends CWebModule {
	/**
	 * The name of the tag model.
	 * @var string
	 */
	public $tagModelClass = "ATag";
	/**
	 * The name of the tags table
	 * @var string
	 */
	public $tagTableName = "tags";
	/**
	 * The name of the tag assignments model
	 * @var string
	 */
	public $tagAssignmentModelClass = "ATagAssignment";

	/**
	 * The name of the tag assignments table
	 * @var string
	 */
	public $tagAssignmentTableName = "tagassignments";
}