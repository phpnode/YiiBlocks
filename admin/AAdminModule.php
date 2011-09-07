<?php
Yii::import("packages.admin.components.*");
/**
 * Provides administration functions
 * @package packages.admin
 * @author Charles Pick
 */
class AAdminModule extends CWebModule {
	/**
	 * The menu items to show for this module.
	 * These menu items will be shown in the sidebar in the admin interface
	 * @see CMenu::$items
	 * @var array
	 */
	public $menuItems = array();

	public function getMainMenu() {
		$menuItems = array();
		$menuItems = CMap::mergeArray($menuItems,$this->menuItems);
		foreach(array_keys($this->getModules()) as $name) {
			$module = $this->getModule($name);
			if (!isset($module->menuItems)) {
				continue;
			}
			$menuItems = CMap::mergeArray($menuItems,$module->menuItems);
		}
		return $menuItems;
	}
	/**
	 * Holds the baseUrl of the assets
	 * @var string
	 */
	protected $_assetBaseUrl;
	/**
	 * The name of the default controller
	 * @var string
	 */
	public $defaultController = "admin";

	/**
	 * The pre-filter for controller actions.
	 *
	 * @param CController $controller the controller
	 * @param CAction $action the action
	 * @return boolean whether the action should be executed.
	 */
	public function beforeControllerAction($controller,$action) {
		return parent::beforeControllerAction($controller,$action);
	}
	/**
	 * Gets the base url for the admin assets
	 * @return string the base url for the admin assets
	 */
	public function getAssetBaseUrl() {
		if ($this->_assetBaseUrl === null) {
			$alias = Yii::getPathOfAlias("packages.admin.assets");
			$this->_assetBaseUrl = Yii::app()->assetManager->publish($alias);
		}
		return $this->_assetBaseUrl;
	}
	/**
	 * Sets the base url for the admin assets
	 * @param string $url the admin assets base url
	 * @return string the admin assets base url
	 */
	public function setAssetBaseUrl($url) {
		return $this->_assetBaseUrl = $url;
	}


}