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
			$directories = AFileHelper::findDirectories(Yii::getPathOfAlias("packages"),array(
				"level" => 0,
			));
			foreach($directories as $dir) {
				if (file_exists($dir."/package.json")) {
					$package = APackage::load(basename($dir));
					if (is_object($package)) {
						$this->_packages->add($package->name,$package);
					}
				}
			}
		}
		return $this->_packages;
	}
	/**
	 * Gets a list of authenticated package repositories
	 * @return CAttributeCollection  a list of authenticated package repositories
	 */
	public function getRepositories() {
		if ($this->_repositories === null) {
			$this->_repositories = new CAttributeCollection();
			$directories = AFileHelper::findDirectories(Yii::getPathOfAlias("packages.ypm.repositories"),array(
				"level" => 0,
			));
			foreach($directories as $dir) {
				if (file_exists($dir."/repository.json")) {
					$repo = APackageRepository::load(basename($dir));
					if (is_object($repo)) {
						$this->_repositories->add($repo->name,$repo);
					}
				}
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

		$package = $this->find($packageName);
		if ($package === false) {
			return false;
		}
		else {
			return $package->install();
		}
	}
	/**
	 * Finds a package based on the given name.
	 * @param string $packageName the name of the package to find
	 * @return mixed Either an instance of APackage
	 */
	public function find($packageName) {
		if (strstr($packageName,"/")) {
			$repoName = explode("/",$packageName);
			$packageName = array_pop($repoName);
			$repoName = array_shift($repoName);
			if (!isset($this->repositories->{$repoName})) {
				return false;
			}
			$repository = $this->repositories->{$repoName};

			if (!isset($repository->packages->{$packageName})) {
				return false;
			}
			return $repository->packages->{$packageName};
		}
		else {
			if (!isset($this->packages->{$packageName})) {
				// this isn't an installed package so we need to find it
				foreach($this->repositories as $repository) {
					if (isset($repository->packages->{$packageName})) {
						return $repository->packages->{$packageName};
					}
				}
				return false;
			}
			else {
				return $this->packages->{$packageName};
			}
		}

	}
}
