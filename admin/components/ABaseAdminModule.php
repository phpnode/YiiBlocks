<?php
/**
 * A base class for admin modules
 * @author Charles Pick
 * @package packages.admin.components
 */
abstract class ABaseAdminModule extends CWebModule {
	/**
	 * Holds the menu items to show for this module.
	 * These menu items will be shown in the sidebar in the admin interface
	 * @see CMenu::$items
	 * @var array
	 */
	protected $_menuItems = array();
	/**
	 * Sets the menu items for this module.
	 * @see CMenu::$items
	 * @param array $value the menu items for this module
	 * @return array the menu items for this module
	 */
	public function setMenuItems($value) {
		return $this->_menuItems = $value;
	}
	/**
	 * Gets the menu items for this module.
	 * If this module is the module for the current controller, the first menu item will activated!
	 * @see CMenu::$items
	 * @return array the menu items
	 */
	public function getMenuItems() {
		$menuItems = $this->_menuItems;
		if (count($menuItems) && isset(Yii::app()->controller->module) && Yii::app()->controller->module === $this) {
			$menuItems[0]['active'] = true;
		}
		return $menuItems;
	}
}