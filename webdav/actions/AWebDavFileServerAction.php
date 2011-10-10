<?php
/**
 * Makes a directory accessible via the webdav protocol
 * @author Charles Pick
 * @package packages.webdav.components
 */
class AWebDavFileServerAction extends CAction {
	/**
	 * The path to the root directory
	 * @var string
	 */
	protected $_directoryPath;
	/**
	 * The path to the temp directory
	 * @var string
	 */
	protected $_tempPath;
	/**
	 * Runs the webdav file server
	 */
	public function run() {
		$controller = $this->getController();
		// Create the root node
		$root = new Sabre_DAV_FS_Directory($this->getDirectoryPath());

		// The rootnode needs in turn to be passed to the server class
		$server = new Sabre_DAV_Server($root);

		$server->setBaseUri($controller->createUrl("/".$controller->getRoute()));

		// Support for LOCK and UNLOCK
		$lockBackend = new Sabre_DAV_Locks_Backend_File($this->getTempPath() . '/locksdb');
		$lockPlugin = new Sabre_DAV_Locks_Plugin($lockBackend);
		$server->addPlugin($lockPlugin);

		// Support for html frontend
		$browser = new AWebDavBrowserPlugin();
		$server->addPlugin($browser);

		// Authentication backend
		$authBackend = new AUserDavAuth();
		$auth = new Sabre_DAV_Auth_Plugin($authBackend,'SabreDAV');
		$server->addPlugin($auth);

		// Temporary file filter
		$tempFF = new Sabre_DAV_TemporaryFileFilterPlugin($this->getTempPath());
		$server->addPlugin($tempFF);
		// And off we go!
		$server->exec();
	}

	/**
	 * Sets the root directory path
	 * @param string $directoryPath the path to the root directory
	 */
	public function setDirectoryPath($directoryPath)
	{
		$this->_directoryPath = $directoryPath;
	}

	/**
	 * Gets the root directory path
	 * @return string the root directory path
	 */
	public function getDirectoryPath()
	{
		return $this->_directoryPath;
	}

	/**
	 * Sets the path to the temporary directory
	 * @param string $tempPath
	 */
	public function setTempPath($tempPath)
	{
		$this->_tempPath = $tempPath;
	}

	/**
	 * Gets the path to the temporary directory
	 * @return string
	 */
	public function getTempPath()
	{
		if ($this->_tempPath === null) {
			$this->_tempPath = Yii::getPathOfAlias("application.runtime")."/webdav/temp";
			if (!file_exists($this->_tempPath)) {
				mkdir($this->_tempPath,0777,true);
			}
		}
		return $this->_tempPath;
	}

}