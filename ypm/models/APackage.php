<?php
/**
 * APackage represents information about a Yii Package that can be installed from
 * an external source.
 * @author Charles Pick
 * @package packages.ypm.models
 */
class APackage extends CFormModel {
	/**
	 * Constructor.
	 * @param string $scenario name of the scenario that this model is used in.
	 * See {@link CModel::scenario} on how scenario is used by models.
	 * @see getScenario
	 */
	public function __construct($scenario='create')
	{
		parent::__construct($scenario);
	}
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
	 * The name of the repository this package belongs to
	 * @var string
	 */
	public $repositoryName = "local";

	/**
	 * The current version of this package.
	 * Defaults to 1.
	 * @var mixed
	 */
	public $version = 1;

	/**
	 * An array of files that make up this package.
	 * A full path to the file should be specified, but the file must be located
	 * under the application packages basePath, e.g. in /protected/packages
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
	 * The path to the directory for this package
	 * @var string
	 */
	protected $_baseDir;



	/**
	 * The validation rules for packages
	 * @see CModel::rules()
	 * @return array The validation rules for this model
	 */
	public function rules() {
		return array(
			array("name","required",),
			array("description","required"),
			array("name", "length", "max" => 50,),
			array("description", "length", "max" => 1000),
			array('name', 'match', 'pattern'=>'/^([a-zA-Z0-9-])+$/'),
			array('name','checkUnique',"on" => "create"),
			array("files", "safe", "on" => "edit"),
		);
	}

	/**
	 * Checks that the package name is unique
	 * @return boolean true if the name is unique
	 */
	public function checkUnique() {
		if ($this->hasErrors("name")) {
			return false;
		}
		$package = $this->getManager()->find($this->name);
		if ($package === false) {
			return true;
		}
		$this->addError("name",Yii::t("packages.ypm","A package with this name already exists!"));
		return false;
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
		return isset($this->getManager()->getPackages()->{$this->name});
	}
	/**
	 * Gets the package repository that this package belongs to
	 * @return APackageRepository the repo for this package
	 */
	public function getRepository() {
		return $this->getManager()->getRepositories()->itemAt($this->repositoryName);
	}
	/**
	 * Gets the package manager
	 * @return APackageManager the package manager
	 */
	public function getManager() {
		return Yii::app()->packageManager;
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

		$packages = array();
		foreach($this->dependencies as $packageName) {
			$package = $this->getManager()->find($packageName);
			if ($package === false) {
				throw new APackageRepositoryException(Yii::t("packages.ypm", "No such package: {name}", array("{name}" => $packageName)));
			}
			$packages[] = $package;
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
	 * Delete the package and all associated files
	 * @return boolean whether the package was completely deleted or not
	 */
	public function delete() {
		$dir = $this->getBaseDir();
		$deleted = true;
		$filesToDelete = array();
		$filesToDelete[] = $dir."/package.json";
		foreach($this->files as $file) {
			$filesToDelete[] = $dir.$file;
		}
		foreach($filesToDelete as $file) {
			if (file_exists($file)) {
				$this->log("info","Deleting file: $file");
				$deleted = @unlink($file) && $deleted;
			}
		}
		$directoriesToDelete = AFileHelper::findDirectories($dir);
		$directoriesToDelete[] = $dir;
		$keys = array_map('strlen', $directoriesToDelete);
		array_multisort($keys, SORT_DESC, $directoriesToDelete);

		foreach($directoriesToDelete as $directory) {
			$this->log("info","Deleting directory: $directory");
			$deleted = @rmdir($directory) && $deleted;
		}

		if ($deleted) {
			$this->getRepository()->removePackage($this);
		}
		return $deleted;
	}

	/**
	 * Saves the package JSON to packages/PACKAGENAME/package.json
	 * @param boolean $runValidation Whether to run validation or not, defaults to true.
	 * @return boolean Whether save succeeded or not
	 */
	public function save($runValidation = true) {

		if ($runValidation && !$this->validate()) {
			return false;
		}
		$packageDir = $this->getBaseDir();
		$packageFile = $packageDir."/package.json";
		if (!file_exists($packageDir)) {
			mkdir($packageDir);
		}
		if (function_exists("json_encode")) {
			$json = json_encode($this->toJSON());
		}
		else {
			$json = CJSON::encode($this->toJSON());
		}

		if (!file_put_contents($packageFile, $json)) {
			return false;
		}

		return true;

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
	/**
	 * Gets the attributes to include when converting this item to JSON
	 * This does not return a JSON string, but an array of attributes that should be converted to JSON
	 * @return array The attributes to encode with JSON
	 */
	public function toJSON() {
		return array(
			"name" => $this->name,
			"description" => $this->description,
			"dependencies" => $this->dependencies,
			"files" => $this->files,
			"repositoryName" => $this->repositoryName,
		);

	}

	/**
	 * Loads a package based on the given name.
	 * @return APackage|false the loaded package or false if the package doesn't exist
	 */
	public static function load($name) {
		$package = new APackage("edit");
		$package->name = $name;
		$packageDir = $package->getBaseDir();
		if (!file_exists($packageDir."/package.json")) {
			return false;
		}
		else {
			$json = file_get_contents($packageDir."/package.json");
			if (function_exists("json_decode")) {
				$json = json_decode($json);
			}
			else {
				$json = CJSON::decode($json);
			}
			if (!$json) {
				return false;
			}
			foreach($json as $attribute => $value) {
				$package->{$attribute} = $value;
			}
			return $package;
		}
	}

	/**
	 * Sets the directory that this package is installed in
	 * @param string $directory
	 */
	public function setBaseDir($directory)
	{
		$this->_baseDir = rtrim($directory,"\\/");
	}

	/**
	 * Gets the directory that this package is installed in
	 * @return string
	 */
	public function getBaseDir()
	{
		if ($this->_baseDir === null) {
			$this->_baseDir = Yii::getPathOfAlias("packages")."/".$this->name;
		}
		return $this->_baseDir;
	}
	/**
	 * Imports an entire directory into the package
	 * @param string $path the path to the directory to import
	 * @param null $targetSubDir
	 * @return APackage $this with the directories imported
	 */
	public function importDirectory($path, $targetSubDir = null) {
		$dir = $this->getBaseDir();
		if ($targetSubDir !== null) {
			$targetSubDir = "/".trim($targetSubDir,"/\\");
			$dir .= $targetSubDir;
		}
		if (!file_exists($dir)) {
			mkdir($dir,0777, true);
		}
		CFileHelper::copyDirectory($path,$dir);
		foreach(CFileHelper::findFiles($path) as $file) {
			$file = substr($file,strlen($path));
			if (!in_array($file,$this->files)) {
				$this->files[] = $targetSubDir.$file;
			}
		}
		return $this;
	}

	/**
	 * Imports a file
	 * @param string $path the path to the file to import
	 * @param string|null $targetSubDir the sub directory in the package to import the file into, if null the package base dir will be used
	 * @return APackage $this with the directories imported
	 */
	public function importFile($path, $targetSubDir = null) {
		$dir = $this->getBaseDir();
		if ($targetSubDir !== null) {
			$targetSubDir = "/".trim($targetSubDir,"/\\");
			$dir .= $targetSubDir;
		}
		if (!file_exists($dir)) {
			mkdir($dir,0777, true);
		}
		$file = substr($path,strlen(dirname($path)));
		copy($path,$dir.$file);


		if (!in_array($file,$this->files)) {
			$this->files[] = $targetSubDir.$file;
		}
		return $this;
	}


}
