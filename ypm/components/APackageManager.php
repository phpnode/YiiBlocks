<?php
/**
 * The package manager handles installing, upgrading and removing Yii Packages.
 * 
 * @author Charles Pick
 * @package packages.ypm.components
 */
class APackageManager extends CApplicationComponent {
	/**
	 * Holds an array of package repositories.
	 * @var APackageRepository[]
	 */
	protected $_repositories;
	
	/**
	 * Holds a list of installed packages.
	 * @see getPackages
	 * @var APackage[] 
	 */
	protected $_packages;
	
	/**
	 * Gets a list of installed packages
	 * @return APackage[]
	 */
	public function getPackages() {
		if ($this->_packages === null) {
			$this->_packages = new CAttributeCollection();
			foreach(include(Yii::getPathOfAlias("packages.ypm.data.packages").".php") as $item) {
				$package = new APackage;
				foreach($item as $attribute => $value) {
					$package->{$attribute} = $value;
				}
				$this->_packages->add($package->name,$package);
			}
		}
		return $this->_packages;
	}
	/**
	 * Gets a list of authenticated package repositories
	 * @return APackageRepository[] a list of authenticated package repositories
	 */
	public function getRepositories() {
		if ($this->_repositories === null) {
			$this->_repositories = new CAttributeCollection();
			foreach(include(Yii::getPathOfAlias("packages.ypm.data.repositories").".php") as $item) {
				$repo = new APackageRepository();
				foreach($item as $attribute => $value) {
					$repo->{$attribute} = $value;
				}
				$this->_repositories->add($repo->name, $repo);
			}
		}
		return $this->_repositories;
	}
	
	/**
	 * Finds and installs a package based on the given name.
	 * @param string $packageName the name of the package to install
	 * @return boolean whether the installation succeeded or not
	 */
	public function install($packageName) {
		foreach($this->getRepositories() as $repo) {
			if (isset($repo->packages[$packageName])) {
				return $repo->packages[$packageName]->install();
			}
		}
		return false;
	}
}
