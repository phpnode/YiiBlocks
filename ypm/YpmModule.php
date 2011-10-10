<?php
Yii::import("packages.ypm.components.*");
Yii::import("packages.ypm.models.*");
Yii::import("packages.ypm.exceptions.*");
/**
 * Holds Yii Package Manager functionality
 * @package packages.ypm
 * @author Charles Pick
 */
class YpmModule extends ABaseAdminModule {

	/**
	 * The menu items to show for this module.
	 * These menu items will be shown in the sidebar in the admin interface
	 * @see CMenu::$items
	 * @var array
	 */
	protected $_menuItems = array(
		array(
			"label" => "Package Manager",
			"url" => array("/admin/ypm/default/index"),
			"linkOptions" => array(
				"class" => "package icon",
			),
		)
	);


}
