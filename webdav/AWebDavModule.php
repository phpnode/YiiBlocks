<?php
// First we need to register the sabredav auto loader
$path = Yii::getPathOfAlias("packages.webdav.vendors.SabreDAV.lib.Sabre.autoload").".php";
spl_autoload_unregister(array('YiiBase','autoload'));
require_once $path;
spl_autoload_register(array('YiiBase','autoload'));

Yii::import("packages.webdav.components.*");
/**
 * Allows webdav integration
 * @author Charles Pick
 * @package packages.webdav
 */
class AWebDavModule extends CWebModule {
	protected $_fileUrl;
	protected $_calenderUrl;
	protected $_contactsUrl;
	protected $_todoUrl;
	/**
	 * The directory path that contains webdav assets
	 * @var string
	 */
	protected $_directoryPath;

	/**
	 * Sets the directory path that contains webdav assets
	 * @param string $directoryPath the path to the webdav assets
	 */
	public function setDirectoryPath($directoryPath) {
		$directoryPath = realpath($directoryPath).DIRECTORY_SEPARATOR;
		$this->_directoryPath = $directoryPath;
	}

	/**
	 * Gets the directory path that contains webdav assets
	 * @return string the directory path that contains the webdav assets
	 */
	public function getDirectoryPath() {
		if ($this->_directoryPath === null) {
			$this->setDirectoryPath(__DIR__.DIRECTORY_SEPARATOR."data");
		}
		return $this->_directoryPath;
	}
	/**
	 * Triggered before a controller action runs
	 * @param CController $controller the controller on which to run the action
	 * @param CAction $action the action to run
	 * @return boolean whether the action should be run or not
	 */
	public function beforeControllerAction(CController $controller, CAction $action) {
		// turn off any log routes that might affect the output
		foreach(Yii::app()->log->routes as $logRoute) {
			if ($logRoute instanceof CProfileLogRoute || $logRoute instanceof CWebLogRoute) {
				$logRoute->enabled = false;
			}
		}
		return parent::beforeControllerAction($controller, $action);
	}
}