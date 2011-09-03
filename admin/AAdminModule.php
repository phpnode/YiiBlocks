<?php
Yii::import("packages.admin.components.*");
/**
 * Provides administration functions
 * @package packages.admin
 * @author Charles Pick
 */
class AAdminModule extends CWebModule {
	public $mainMenu = array();
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