<?php
/**
 * Represents documentation information about an interface.
 * @author Charles Pick
 * @package packages.docs
 */
class AInterfaceDoc extends ATypeDoc {
		
	/**
	 * The version of the interface
	 * @var mixed
	 */
	public $version;
	
	/**
	 * The author of this interface, either a string or an array if there are multiple authors
	 * @var string|array
	 */
	public $author;
	
	/**
	 * Whether this is an abstract interface or not
	 * @var boolean
	 */
	public $isAbstract = false;
	

	/**
	 * The name of the class that this interface extends.
	 * @var string
	 */
	public $extends;
	
	
	/**
	 * A list of methods that belong to this interface
	 * @var AClassMethodDoc[]
	 */
	public $methods;
	
	
	/**
	 * A list of constants that belong to this interface
	 * @var AClassConstantDoc[]
	 */
	public $constants;	
	
	/**
	 * Constructor, sets the item attributes
	 * @see ATypeDoc::__construct()
	 * @param array $config The configuration attribute => array
	 */
	public function __construct($config = null) {
		parent::__construct($config);
		$properties = array("methods","constants");
		foreach($properties as $property) {
			if (!$this->{$property} instanceof CAttributeCollection) {
				$this->{$property} = new CAttributeCollection($this->{$property});
			}
		}
	}
	/**
	 * Gets the parent for this interface
	 * @return AInterfaceDoc the parent interface
	 */
	public function getParent() {
		if ($this->extends == "") {
			return false;
		}
		return $this->namespace->findInterface(trim($this->extends));
	}
	
	/**
	 * Gets all the members with the specified type, from this interface and its parents.
	 * @param string $type the type, e.g. "properties", "methods" or "constants", this should be the name of a class property
	 * @param string $filter the filter to use, if any. The should be the name of a boolean property on the class which should be truthy if the member is to be included in the results
	 * @return ATypeDoc[] All the matching members
	 */
	public function allMembers($type, $filter = null) {
		if (!isset($this->{$type})) {
			return array();
		}
		$parent = $this->parent;
		if ($parent !== false) {
			$members = $parent->allMembers($type,$filter);
		}
		else {
			$members = array();
		}
		foreach($this->{$type} as $member) {
			if ($filter === null || $member->{$filter}) {
				$members[$member->name] = $member;
			}
		}
		ksort($members);
		return $members;
	}
	
	/**
	 * Parses the doc comment
	 */
	protected function parseDocComment() {
		if ($this->comment != "") {
			parent::parseDocComment();
			foreach($this->tags as $tag) {
				if ($tag->tagName == "author") {
					if (is_array($this->author)) {
						$this->author[] = $tag->value;
					}
					else if ($this->author != "") {
						$this->author = array($this->author);
						$this->author[] = $tag->value;
					}
					else {
						$this->author = $tag->value;
					}
				}
				elseif ($tag->tagName == "version" && $this->version == "") {
					$this->version = $tag->value;
				}
				 
			}
		}
	}
	
	/**
	 * Processes the interface.
	 * @see ATypeDoc::process()
	 * @return boolean Whether this was processed or not
	 */
	public function process() {
		foreach($this->constants as $constant) {
			$constant->process();
		}
		foreach($this->methods as $method) {
			$method->process();
		}
		return parent::process();
	}
	
	/**
	 * Gets the signature for this interface
	 * @return string the signature to display for this interface
	 */
	public function signature() {
		$signature = "";

		if ($this->isAbstract) {
			$signature .= "abstract ";
		}
		
		$signature .= 'interface '.$this->name;
		if (count($this->extends)) {
			$signature .= " extends ".implode(", ",$this->extends);
		}
		if (count($this->implements)) {
			$signature .= " implements ".implode(", ",$this->implements);
		}
		
		return $signature;
	}
	
	/**
	 * Checks the documentation for this type
	 * @return boolean true if no errors were logged, otherwise false;
	 */
	public function check() {
		$hasErrors = !parent::check();
		if ($this->package == "") {
			$this->log("info","No package for class/interface".$this->name,$this->startLine,$this->endLine);
			$hasErrors = true;
		}
		foreach($this->constants as $constant) {
			if (!$constant->check()) {
				$hasErrors = true;
			}
		}
	
		foreach($this->methods as $method) {
			if (!$method->check()) {
				$hasErrors = true;
			}
		}
		
		return !$hasErrors;
	}
	/**
	 * Gets the descendants for this interface
	 * @return AInterfaceDoc[] the interfaces that extend this one
	 */
	public function getDescendants() {
		$descendants = array();
		foreach($this->namespace->interfaces as $interface) {
			if ($interface->extends == $this->name) {
				$descendants[] = $interface;
			}
		}
		return $descendants;
	}
	
	/**
	 * Gets the value of the author tag(s) for this item
	 * @return array the value of the author tags
	 */
	public function getAuthors() {
		$authors = array();
		foreach($this->tags as $tag) {
			if (strtolower($tag->tagName) == "author") {
				$authors[] = $tag->value;
			}
		}
		return $authors;
	}
	
	/**
	 * Gets the documentation filename for this interface
	 * @return string the doc file name
	 */
	public function getDocFilename() {
		return $this->name."-interface.html";
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
		if ($fragment === null) {
			if ($type == $this->name) {
				return CHtml::link($label,$this->docFilename);
			}
			// see if there is a constant or method on this interface
			$all = $this->allMembers("constants");
			if (isset($all[$type])) {
				return CHtml::link($label,$all[$type]->docFilename."#".$type."-constant");
			}
			$all = $this->allMembers("methods");
			if (isset($all[$type])) {
				return CHtml::link($label,$all[$type]->docFilename."#".$type."-method");
			}
		}
		else if ($type == $this->name) {
			// see if there is a constant or method on this interface
			$all = $this->allMembers("constants");
			if (isset($all[$fragment])) {
				return CHtml::link($label,$all[$fragment]->docFilename."#".$fragment."-constant");
			}
			$all = $this->allMembers("methods");
			if (isset($all[$fragment])) {
				return CHtml::link($label,$all[$fragment]->docFilename."#".$fragment."-method");
			}
			return $label;
		}
		return $this->namespace->typeLink($rawType,$label);
		
	}
}
