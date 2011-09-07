<?php

/**
 * Provides administration functionality for Role Based Access Control features.
 * @author Charles Pick
 * @package packages.rbac
 */
class ARbacModule extends ABaseAdminModule {
	/**
	 * The menu items to show for this module.
	 * These menu items will be shown in the sidebar in the admin interface
	 * @see CMenu::$items
	 * @var array
	 */
	protected $_menuItems = array(
		array(
			"label" => "Role Based Access Control",
			"url" => array("/admin/rbac/rbac/index"),
			"linkOptions" => array(
				"class" => "group icon",
			),
			"items" => array(
				array(
					"label" => "Roles",
					"url" => array("/admin/rbac/role/index"),
				),
				array(
					"label" => "Tasks",
					"url" => array("/admin/rbac/task/index"),
				),
				array(
					"label" => "Operations",
					"url" => array("/admin/rbac/operation/index"),
				)
			)
		)
	);

	public $defaultController = "rbac";

}
