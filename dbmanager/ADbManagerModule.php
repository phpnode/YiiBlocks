<?php
Yii::import("packages.dbmanager.components.*");
Yii::import("packages.dbmanager.models.*");
/**
 * Provides an interface for managing the database
 * @package packages.dbmanager
 * @author Charles Pick
 */
class ADbManagerModule extends ABaseAdminModule {
	/**
	 * Sets the default controller for the module
	 * @var string
	 */
	public $defaultController = "table";

	/**
	 * The menu items to show for this module.
	 * These menu items will be shown in the sidebar in the admin interface
	 * @see CMenu::$items
	 * @var array
	 */
	protected $_menuItems = array(
		array(
			"label" => "Database",
			"url" => array("/admin/dbmanager/table/index"),
			"linkOptions" => array(
				"class" => "database icon",
			),
			"items" => array()
		)
	);

	public function beforeControllerAction($controller, $action) {
		foreach(Yii::app()->db->getSchema()->getTableNames() as $table) {
			$this->_menuItems[0]['items'][] = array(
				"label" => $table,
				"url" => array("/admin/dbmanager/table/view","name" => $table),
			);
		}
		parent::beforeControllerAction($controller,$action);
	}
}