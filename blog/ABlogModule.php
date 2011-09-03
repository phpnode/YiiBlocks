<?php
// We import the module level components immediately
Yii::import("packages.blog.models.*");
Yii::import("packages.blog.components.*");

/**
 * The blog module.
 * @package packages.blog
 * @author Charles Pick
 */
class ABlogModule extends CWebModule {

	/**
	 * The name of the post table
	 * Defaults to "posts"
	 * @type string
	 */
	public $postTable = "posts";


	/**
	 * The name of the post to tags pivot table
	 * Defaults to "posttags"
	 * @type string
	 */
	public $postTagTable = "posttags";



	/**
	 * The default controller for this module
	 * @var string
	 */
	public $defaultController = "post";






}
