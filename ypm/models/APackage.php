<?php
/**
 * APackage represents information about a Yii Package that can be installed from
 * an external source.
 * @author Charles Pick
 * @package packages.ypm.models
 */
class APackage extends CFormModel {
	
	
	/**
	 * The unique name of the package.
	 * @var string
	 */
	public $name;
	
	/**
	 * A short description of this package
	 * @var string
	 */
	public $description;
	
	/**
	 * The version of this package.
	 * Defaults to 1.
	 * @var mixed
	 */
	public $version = 1;
	
	/**
	 * An array of files that make up this package.
	 * A full path to the file should be specified, but the file must be located
	 * under the application basePath, e.g. in /protected
	 * <pre>
	 * array(
	 * 	"/var/www/testdrive/protected/testpackage/TestModule.php",
	 *  "/var/www/testdrive/protected/testpackage/components/TestBehavior.php",
	 * )
	 * </pre>
	 * @var array 
	 */
	public $files = array();
	/**
	 * An array of other packages that this package depends on.
	 * @var array
	 */
	public $dependencies = array();
	
	
	
	/**
	 * Holds the package repository that this package belongs to.
	 * @var APackageRepository
	 */
	protected $_repository;
	
	/**
	 * The validation rules for packages
	 * @see CModel::rules()
	 * @return array The validation rules for this model
	 */
	public function rules() {
		return array(
			array("name, description","required"),
			array("name", "length", "max" => 50),
			array("description", "length", "max" => 1000),
			array('name', 'match', 'pattern'=>'/^([a-z0-9-])+$/'),
		);
	}
	
	/**
	 * Gets an array of attribute labels for this model
	 * @see CModel::attributeLabels
	 * @return array attribute => label
	 */
	public function attributeLabels() {
		return array(
			"name" => "Package Name"
		);
	}
	
	/**
	 * Add our custom validation checks
	 * @see CModel::beforeValidate()
	 */
	public function beforeValidate() {
		return parent::beforeValidate();
	}
	
	/**
	 * Determines whether this package is installed or not.
	 * @return boolean true if the package is installed
	 */
	public function getIsInstalled() {
		return isset(Yii::app()->packageManager->packages->{$this->name});
	}	
	/**
	 * Gets the package repository that this package belongs to
	 * @return APackageRepository the repo for this package
	 */
	public function getRepository() {
		return $this->_repository;
	}
	
	/**
	 * Sets the package repository that this package belongs to
	 * @param mixed $repo The repository, either an instance of APackageRepository or a configuration array
	 */
	public function setRepository($repo) {
		if ($repo instanceof APackageRepository) {
			$this->_repository = $repo;
		}
		else {
			$this->_repository = new APackageRepository();
			foreach($repo as $attribute => $value) {
				$this->_repository->{$attribute} = $value;
			}
		}
		return $this->_repository;
	}
	
	
	/**
	 * Installs the package
	 * @param string $version The version of the package to install
	 * @return boolean whether the installation succeeded or not
	 */
	public function install($version = "stable") {
		if ($this->isInstalled && $this->version == $version) {
			$this->log("error",Yii::t("packages.ypm","Couldn't install {name} because it is already installed!",array("{name}" => $this->name)));
			return false;
		}
		$this->log("info",Yii::t("packages.ypm","Installing Package: {name}",array("{name}" => $this->name)));
		if (count($this->dependencies)) {
			$this->log("info", Yii::t("packages.ypm","Resolving dependencies..."));
			try {
				$dependencies = $this->resolveDependencies();
				foreach($dependencies as $dependency) {
					if ($dependency->isInstalled) {
						$this->log("info",Yii::t("packages.ypm","Package {name} OK.",array("{name}" => $dependency->name)));
					}
					else {
						if (!$dependency->install()) {
							$this->log("error",Yii::t("packages.ypm","Failed to install package: {name}.",array("{name}" => $dependency->name)));
							return false;
						}
					}
				}
			}
			catch (APackageRepositoryException $e) {
				$this->log("error",$e->getMessage());
				return false;
			}
		}
		$this->log("info",Yii::t("packages.ypm","Downloading {name} from the repository ({repo})...", array("{name}" => $this->name, "{repo}" => $this->repository->name)));
		if (!$this->download($version)) {
			$this->log("error",Yii::t("packages.ypm","There was an error downloading {name} from the repository ({repo}).", array("{name}" => $this->name, "{repo}" => $this->repository->name)));
			return false;
		}
		$this->log("info", Yii::t("packages.ypm", "Package {name} installed successfully.", array("{name}" => $this->name)));
		return true;
	}
	/**
	 * Downloads the package from the repository.
	 * @param string $version The version of the package to download
	 * @return boolean true if the download succeeded
	 */
	public function download($version = "stable") {
		$url = rtrim($this->repository->url,"/")."/".urlencode($this->name)."/versions/".urlencode($version);
		$this->log("info",Yii::t("packages.ypm","Downloading {url} ...",array("{url}" => $url)));
		
		return true;
	}
	/**
	 * Resolves the dependencies for this package
	 * @return APackage[] an array of packages that this package depends on
	 */
	public function resolveDependencies() {
		$packageManager = Yii::app()->packageManager;
		$packages = array();
		foreach($this->dependencies as $dependency) {
			if (strstr($dependency,"/")) {
				$repoName = explode("/",$dependency);
				$dependency = array_pop($repoName);
				$repoName = array_shift($repoName);
				if (!isset($packageManager->repositories->{$repoName})) {
					throw new APackageRepositoryException(Yii::t("packages.ypm", "No such package repository: {repoName}", array("{repoName}" => $repoName)));
				}
				$repository = $packageManager->repositories->{$repoName};
				
				if (!isset($repository->packages->{$dependency})) {
					throw new APackageRepositoryException(Yii::t("packages.ypm", "The repository: {repoName} does not contain a package called {package}", array("{repoName}" => $repoName, "{package}" => $dependency)));
				}
				$packages[] = $repository->packages->{$dependency};
			}
			else {
				if (!isset($packageManager->packages->{$dependency})) {
					// this isn't an installed package so we need to find it
					$matched = false;
					foreach($packageManager->repositories as $repository) {
						if (isset($repository->packages->{$dependency})) {
							$matched = true;
							$packages[] = $repository->packages->{$dependency};
							break;
						}
					}
					if (!$matched) {
						throw new APackageRepositoryException(Yii::t("packages.ypm", "No such package: {dependency}", array("{dependency}" => $dependency)));
					}
				}
				else {
					$packages[] = $packageManager->packages->{$dependency};
				}
			}
		}
		return $packages;
	}
	
	/**
	 * Uninstalls the package
	 * @return boolean whether uninstallation succeeded or not
	 */
	public function uninstall() {
		
	}
	
	/**
	 * Upgrades the package
	 * @return boolean whether the upgrade succeeded or not
	 */
	public function upgrade() {
		
	}
	/**
	 * Adds a message with the given level to the log.
	 * @param string $level The log level, either "info", "success", "warning" or "error"
	 * @param string $message The message to log
	 */
	public function log($level, $message) {
		#echo "<pre>\n";
		echo "[$level] - $message\n";
		#echo "</pre>\n";
	}
}
