<?php
/**
 * APackageRepository represents information about a Yii Package Repository
 * @author Charles Pick
 * @package packages.ypm.models
 */
class APackageRepository extends CFormModel {
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
	 * The unique name of the package repository.
	 * @var string
	 */
	public $name;

	/**
	 * A short description of this package
	 * @var string
	 */
	public $description;

	/**
	 * The package repository URL
	 * @var string
	 */
	public $url;

	/**
	 * Holds a list of packages provided by this repo
	 * @see getPackages()
	 * @var APackage[]
	 */
	protected $_packages;

	/**
	 * The path to the directory for this repository
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
			array("url","url"),
			array("name", "length", "max" => 50,),
			array("description", "length", "max" => 1000),
			array('name', 'match', 'pattern'=>'/^([a-zA-Z0-9-])+$/'),
			array('name','checkUnique',"on" => "create"),
		);
	}

	/**
	 * Checks that the repository name is unique
	 * @return boolean true if the name is unique
	 */
	public function checkUnique() {
		if ($this->hasErrors("name")) {
			return false;
		}
		if (!isset(Yii::app()->packageManager->repositories[$this->name])) {
			return true;
		}
		$this->addError("name",Yii::t("packages.ypm","A repository with this name already exists!"));
		return false;
	}
	/**
	 * Gets an array of attribute labels for this model
	 * @see CModel::attributeLabels
	 * @return array attribute => label
	 */
	public function attributeLabels() {
		return array(
			"name" => "Repository Name"
		);
	}
	/**
	 * Determines whether this is a remote repository or not
	 * @return boolean true if this is a remote repository
	 */
	public function getIsRemote() {
		return $this->url != "";
	}
	/**
	 * Delete the package repository
	 * @return boolean whether the repository was completely deleted or not
	 */
	public function delete() {
		$dir = $this->getBaseDir();
		$filesToDelete = CFileHelper::findFiles($dir);;
		$filesToDelete[] = $dir."/repository.json";
		$deleted = true;
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
		return $deleted;
	}

	/**
	 * Saves the repository JSON to repositories/REPOSITORYNAME/repository.json
	 * @param boolean $runValidation Whether to run validation or not, defaults to true.
	 * @return boolean Whether save succeeded or not
	 */
	public function save($runValidation = true) {
		if ($runValidation && !$this->validate()) {
			return false;
		}
		$dir = $this->getBaseDir();
		$repositoryFile = $dir."/repository.json";
		if (!file_exists($dir)) {
			mkdir($dir);
		}
		if (function_exists("json_encode")) {
			$json = json_encode($this->toJSON());
		}
		else {
			$json = CJSON::encode($this->toJSON());
		}
		return file_put_contents($repositoryFile, $json) ? true : false;

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
		$json = array(
			"name" => $this->name,
			"description" => $this->description,
			"url" => $this->url,
			"packages" => array()
		);
		foreach($this->getPackages() as $name => $package) {
			$json['packages'][$name] = $package->toJSON();
		}
		return $json;
	}

	/**
	 * Loads a package repository based on the given name.
	 * @param string $name The name of the repository
	 * @return APackageRepository|false the loaded package or false if the package doesn't exist
	 */
	public static function load($name) {
		$repository = new APackageRepository("edit");
		$repository->name = $name;
		$baseDir = $repository->getBaseDir();
		if (!file_exists($baseDir."/repository.json")) {
			return false;
		}
		else {
			$json = file_get_contents($baseDir."/repository.json");
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
				$repository->{$attribute} = $value;
			}
			return $repository;
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
			$this->_baseDir = Yii::getPathOfAlias("packages.ypm.repositories")."/".$this->name;
		}
		return $this->_baseDir;
	}

	/**
	 * Refreshes the list of packages
	 */
	public function refreshPackages() {
		if ($this->getIsRemote()) {
			$curl = new ACurl;
			$curl->options->timeout = 3;
			$response = $curl->get($this->url)->exec()->fromJSON();
			$this->_packages = new CAttributeCollection();
			foreach($response['packages'] as $item) {
				$package = new APackage();
				foreach($item as $attribute => $value) {
					$package->{$attribute} = $value;
				}
				$this->_packages[$package->name] = $package;
			}
		}
		if (!($this->_packages instanceof CAttributeCollection)) {
			$this->_packages = new CAttributeCollection($this->_packages);
		}
		return $this->_packages;
	}
	/**
	 * Gets an array of packages provided by this repository.
	 * @param boolean $forceRefresh Whether to force a refresh or not, defaults to false.
	 * @return CAttributeCollection an collection of packages provided by this repository
	 */
	public function getPackages($forceRefresh = false) {
		if ($forceRefresh || $this->_packages === null) {
			$this->refreshPackages();
		}

		return $this->_packages;
	}

	/**
	 * Adds a package to the repository
	 * @param APackage $package the package to add
	 * @param boolean $runValidation whether to run validation before saving
	 * @return boolean whether the package was added or not
	 */
	public function addPackage(APackage $package, $runValidation = true) {
		$package->repositoryName = $this->name;
		if (!$package->save($runValidation)) {
			return false;
		}
		$this->getPackages()->add($package->name, $package);
		return $this->save($runValidation);
	}
	/**
	 * Removes a package from the repository
	 * @param APackage $package the package to remove
	 * @param boolean $runValidation whether to run validation before saving
	 * @return boolean whether the package was removed or not
	 */
	public function removePackage(APackage $package, $runValidation = true) {
		$this->getPackages()->remove($package->name);
		return $this->save($runValidation);
	}
	/**
	 * Sets the packages for this repo
	 * @param $packages the packages for this repo
	 * @return $packages the packages for this repo
	 */
	public function setPackages($packages) {
		$collection = new CAttributeCollection();
		foreach($packages as $name => $package) {
			if (!($package instanceof APackage)) {
				$config = (array) $package;
				$config['class'] = "APackage";
				$package = Yii::createComponent($config);
			}
			$collection->add($name,$package);
		}
		return $this->_packages = $collection;
	}
}
