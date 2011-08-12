<?php
/**
 * Represents documentation information about a namespace.
 * @author Charles Pick
 * @package packages.docs
 */
class ANamespaceDoc extends ATypeDoc {
	
	/**
	 * The classes that belong to this namespace
	 * @var AClassDoc[]
	 */
	public $classes;
	
	/**
	 * The interfaces that belong to this namespace
	 * @var AInterfaceDoc[]
	 */
	public $interfaces;
	
	/**
	 * The functions that belong to this namespace
	 * @var AFunctionDoc[]
	 */
	public $functions;
	
	/**
	 * The variables that belong to this namespace
	 * @var AVariableDoc[]
	 */
	public $variables;
	
	/**
	 * The constants that belong to this namespace
	 * @var AConstantDoc[]
	 */
	public $constants;
	
	/**
	 * The child namespaces that belong to this namespace
	 * @var ANamespaceDoc
	 */
	public $namespaces;
	
	/**
	 * Holds the packages found in this namespace
	 * @var APackageDoc[]
	 */
	protected $_packages;
	
	/**
	 * Constructor, sets the item attributes
	 * @see ATypeDoc::__construct()
	 * @param array $config The configuration attribute => array
	 */
	public function __construct($config = null) {
		parent::__construct($config);
		$properties = array("classes", "functions", "variables","constants","namespaces","interfaces");
		foreach($properties as $property) {
			if (!$this->{$property} instanceof CAttributeCollection) {
				$this->{$property} = new CAttributeCollection($this->{$property});
			}
		}
	}
	/**
	 * Gets a list of packages with their contents
	 */
	public function getPackages() {
		if ($this->_packages === null) {
			$this->_packages = array();
			$members = array("interfaces","classes","functions");
			foreach($members as $member) {
				foreach($this->{$member} as $item) {
					if (!isset($this->_packages[$item->package])) {
						$this->_packages[$item->package] = new APackageDoc;
						$this->_packages[$item->package]->name = $item->package;
					}
					$this->_packages[$item->package]->{$member}[$item->name] = $item;
				}
			}
			ksort($this->_packages);
		}
		return $this->_packages;
	}
	
	/**
	 * Processes the namespace.
	 * @see ATypeDoc::process()
	 * @return boolean Whether this was processed or not
	 */
	public function process() {
		foreach($this->constants as $constant) {
			$constant->process();
		}
		foreach($this->namespaces as $namespace) {
			$namespace->process();
		}
		foreach($this->functions as $function) {
			$function->process();
		}
		foreach($this->interfaces as $interface) {
			$interface->process();
		}
		foreach($this->classes as $class) {
			$class->process();
		}
		return parent::process();
	}
	
	/**
	 * Gets the signature for this namespace
	 * @return string the signature to display for this namespace
	 */
	public function signature() {
		return "namespace $this->name";
	}
	
	/**
	 * Checks the documentation for this type
	 * @return boolean true if no errors were logged, otherwise false;
	 */
	public function check() {
		$hasErrors = !parent::check();
		if ($this->name != "") {
			if ($this->package == "") {
				$this->log("info","No package for ".$this->name,$this->startLine,$this->endLine);
				$hasErrors = true;
			}
		}
		foreach($this->constants as $constant) {
			if (!$constant->check()) {
				$hasErrors = true;
			}
		}
		foreach($this->classes as $class) {
			if (!$class->check()) {
				$hasErrors = true;
			}
		}
		foreach($this->functions as $function) {
			if (!$function->check()) {
				$hasErrors = true;
			}
		}
		foreach($this->interfaces as $interface) {
			if (!$interface->check()) {
				$hasErrors = true;
			}
		}
		foreach($this->variables as $variable) {
			if (!$variable->check()) {
				$hasErrors = true;
			}
		}
		foreach($this->namespaces as $namespace) {
			if (!$namespace->check()) {
				$hasErrors = true;
			}
		}
		return !$hasErrors;
	}
	/**
	 * Finds an interface with the specified name
	 * @param string $name the name of the interface
	 * @return AInterfaceDoc the interface, or false if an interface with this name cannot be found
	 */
	public function findInterface($name) {
		if (isset($this->interfaces[$name])) {
			return $this->interfaces[$name];
		}
		return false;
	}
	
	/**
	 * Finds a class with the specified name
	 * @param string $name the name of the class
	 * @return AInterfaceDoc the class, or false if a class with this name cannot be found
	 */
	public function findClass($name) {
		if (isset($this->classes[$name])) {
			return $this->classes[$name];
		}
		return false;
	}
	/**
	 * Gets the documentation filename for this namespace
	 * @return string the doc file name
	 */
	public function getDocFilename() {
		return $this->name."-namespace.html";
	}
	/**
	 * Generates a documentation link for an item of the specified type.
	 * If no such type can be found the type text will be returned instead 
	 * @param string $rawType the type, e.g. CComponent
	 * @param string $label the label for the link, if null the rawType will be used
	 * @return string the link to this type
	 */
	public function typeLink($rawType, $label = null) {
		list ($type,$fragment) = $this->cleanType($rawType);
		if ($label === null) {
			$label = $rawType;
		}
		list ($type,$fragment) = $this->cleanType($rawType);
		if ($fragment === null) {
			// see if there is a constant, function or class on this namespace
			$all = $this->constants;
			if (isset($all[$type])) {
				return CHtml::link($label,$all[$type]->docFilename."#".$type."-constant");
			}
			$all = $this->functions;
			if (isset($all[$type])) {
				return CHtml::link($label,$all[$type]->docFilename);
			}
			
			$all = $this->classes;
			if (isset($all[$type])) {
				return CHtml::link($label,$all[$type]->docFilename);
			}
		}
		else {
			$all = $this->interfaces;
			if (isset($all[$type])) {
				return $all[$type]->typeLink($rawType,$label);
			}
			$all = $this->classes;
			if (isset($all[$type])) {
				return $all[$type]->typeLink($rawType,$label);
			}
		}
		
		return $label;
	}
}
