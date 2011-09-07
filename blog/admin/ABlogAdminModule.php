<?php
// We import the module level components immediately
Yii::import("packages.blog.models.*");
Yii::import("packages.blog.components.*");

/**
 * The blog admin module.
 * @package packages.blog.admin
 * @author Charles Pick
 */
class ABlogAdminModule extends ABaseAdminModule {
	/**
	 * The menu items to show for this module.
	 * These menu items will be shown in the sidebar in the admin interface
	 * @see CMenu::$items
	 * @var array
	 */
	protected $_menuItems = array(
		array(
			"label" => "Blog",
			"url" => array("/admin/blog/blog/index"),
			"linkOptions" => array(
				"class" => "note icon",
			),
			"items" => array(
				array(
					"label" => "Posts",
					"url" => array("/admin/blog/post/index"),
				),
				array(
					"label" => "Feeds",
					"url" => array("/admin/blog/feed/index"),
				),
				array(
					"label" => "Comments",
					"url" => array("/admin/blog/comment/index"),
				),

			)
		)
	);
}
