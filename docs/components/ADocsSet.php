<?php
/**
 * Holds information about a documentation set.
 * @author Charles Pick
 * @package packages.docs.components
 */
class ADocsSet extends CConfiguration {
	/**
	 * The docs generator that this set belongs to
	 * @var ADocsGenerator
	 */
	public $generator;
	/**
	 * The name of the documentation set
	 * @var string
	 */
	public $name;
	/**
	 * The alias of the folder that contains the
	 * documentation sets.
	 * @var string
	 */
	public static $folderAlias = "packages.docs.sets";
	
	/**
	 * Constructor.
	 * @param mixed $data if string, it represents a config file (a PHP script returning the configuration as an array);
	 * If array, it is config data.
	 * @param ADocsGenerator $generator The generator this set belongs to
	 */
	public function __construct($data=null,ADocsGenerator $generator = null) {
		if(is_string($data)) {
			parent::__construct(require($data));
		}
		else {
			parent::__construct($data);
		}
		if ($generator !== null) {
			$this->applyTo($generator);
			$this->generator = $generator;
			$generator->set = $this;
		}
	}
	
	/**
	 * Saves the configuration file
	 * @return boolean Whether the save succeeded or not
	 */
	public function save() {
		$content = "<?php\n";
		$content .= "/**\n";
		$content .= " * The configuration for the $this->name documentation set\n";
		$content .= " */\n";
		$content .= "return ".$this->saveAsString().";";
		$content .= "\n";
		return file_put_contents(Yii::getPathOfAlias(self::$folderAlias)."/".$this->name.".php",$content);
	}
	/**
	 * Loads a documentation generator config set with the given name
	 * @param string $name The name of the set to load
	 * @return ADocsGenerator The doc generator with the settings applied, or false if the set doesn't exist
	 */
	public static function load($name) {
		$generator = new ADocsGenerator;
		$filename = Yii::getPathOfAlias(self::$folderAlias)."/$name.php";
		if (!file_exists($filename)) {
			return false;
		}
		$generator->set = new ADocsSet($filename, $generator);
		$generator->set->name = $name;
		return $generator;
	}
	
	
	
	/**
	 * Saves the configuration into a string.
	 * The string is a valid PHP expression representing the configuration data as an array.
	 * @return string the string representation of the configuration
	 */
	public function saveAsString()
	{
		if (is_object($this->generator)) {
			$this['files'] = $this->generator->files;
			$this['exclude'] = $this->generator->exclude;
		}
		return str_replace("\r",'',var_export($this->toArray(),true));
	}
}
