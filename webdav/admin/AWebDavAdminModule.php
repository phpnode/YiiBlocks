<?php
Yii::import("packages.webdav.AWebDavModule",true);
class AWebDavAdminModule extends ABaseAdminModule {
	/**
	 * The menu items to show for this module.
	 * These menu items will be shown in the sidebar in the admin interface
	 * @see CMenu::$items
	 * @var array
	 */
	protected $_menuItems = array(
		array(
			"label" => "Browse Webdav",
			"url" => array("/admin/webdav/default/index"),
			"linkOptions" => array(
				"class" => "folder icon",
			),
		)
	);
}