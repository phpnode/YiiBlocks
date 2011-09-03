<?php
/**
 * Provides management functionality for resource files.
 * @package packages.resources
 * @author Charles Pick
 */
class AResourceModule extends CWebModule {
	/**
	 * The (protected) directory that holds the resources
	 * @see getResourceDir()
	 * @see setResourceDir()
	 * @var string
	 */
	protected $_resourceDir;
	
	/**
	 * Gets the base directory to store resources in.
	 * This should be a protected directory, outside of the www root.
	 * @return string the resource directory
	 */
	public function getResourceDir() {
		if ($this->_resourceDir === null) {
			$this->_resourceDir = Yii::getPathOfAlias("application.runtime")."/resources";
			if (!file_exists($this->_resourceDir)) {
				mkdir($this->_resourceDir);
			}
		}
		return $this->_resourceDir;
	}
	/**
	 * Sets the base directory to store resources in.
	 * @param string $dir The directory to store resources in, this should be a protected directory, outside of the www root.
	 * @return string the resource directory
	 */
	public function setResourceDir($dir) {
		if (!strstr($dir,DIRECTORY_SEPARATOR)) {
			$dir = Yii::getPathOfAlias($dir);
		}
		return $this->_resourceDir = $dir;
	}
}
