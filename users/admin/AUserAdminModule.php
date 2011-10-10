<?php
/**
 * Provides user administration functions
 * @author Charles Pick
 * @package packages.users.admin
 */
class AUserAdminModule extends ABaseAdminModule {
	/**
	 * The menu items to show for this module.
	 * These menu items will be shown in the sidebar in the admin interface
	 * @see CMenu::$items
	 * @var array
	 */
	protected $_menuItems = array(
		array(
			"label" => "User Administration",
			"url" => array("/admin/users/default/index"),
			"linkOptions" => array(
				"class" => "user icon",
			),
			"items" => array(
				array(
					"label" => "Users",
					"url" => array("/admin/users/user/index"),
				),
				array(
					"label" => "User Groups",
					"url" => array("/admin/users/group/index"),
				),
			)
		)
	);
}