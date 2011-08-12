<?php
/**
 * Represents documentation information about a class.
 * @author Charles Pick
 * @package packages.docs
 */
class AClassDoc extends AInterfaceDoc {
	
	/**
	 * Whether this is an abstract class or not
	 * @var boolean
	 */
	public $isAbstract = false;
	
	/**
	 * Whether this is a final class or not
	 * @var boolean
	 */
	public $isFinal = false;
	
	/**
	 * An array of interface names that this class implements
	 * @var array
	 */
	public $implements = array();

	/**
	 * An array of properties that belong to this class
	 * @var AClassPropertyDoc[]
	 */
	public $properties;
	
	/**
	 * Constructor, sets the item attributes
	 * @see ATypeDoc::__construct()
	 * @param array $config The configuration attribute => array
	 */
	public function __construct($config = null) {
		parent::__construct($config);
		$properties = array("properties");
		foreach($properties as $property) {
			if (!$this->{$property} instanceof CAttributeCollection) {
				$this->{$property} = new CAttributeCollection($this->{$property});
			}
		}
	}
	/**
	 * Processes the class.
	 * @see ATypeDoc::process()
	 * @return boolean Whether this was processed or not
	 */
	public function process() {
		foreach($this->properties as $property) {
			$property->process();
		}
		return parent::process();
	}
	
	/**
	 * Gets the signature for this class
	 * @return string the signature to display for this class
	 */
	public function signature() {
		$signature = "";
		if ($this->isFinal) {
			$signature .= "final ";
		}
		if ($this->isAbstract) {
			$signature .= "abstract ";
		}
		
		$signature .= 'class '.$this->name;
		if (count($this->extends)) {
			$signature .= " extends ".implode(", ",$this->extends);
		}
		if (count($this->implements)) {
			$signature .= " implements ".implode(", ",$this->implements);
		}
		
		return $signature;
	}
	/**
	 * Checks the documentation for this class
	 * @return boolean true if no errors were logged, otherwise false;
	 */
	public function check() {
		$hasErrors = !parent::check();
		
		foreach($this->properties as $property) {
			if (!$property->check()) {
				$hasErrors = true;
			}
		}
		
		
		return !$hasErrors;
	}
	/**
	 * Gets the parent for this class
	 * @return AClassDoc the parent class
	 */
	public function getParent() {
		if ($this->extends == "") {
			return false;
		}
		return $this->namespace->findClass(trim($this->extends));
	}
	
	
	/**
	 * Gets the public properties
	 * @return AClassPropertyDoc[] the public properties
	 */
	public function getPublicProperties() {
		$properties = array();
		foreach($this->properties as $property) {
			if ($property->isPublic) {
				$properties[] = $property;
			}
		}
		return $properties;
	}
	
	
	/**
	 * Gets the descendants for this class
	 * @return AClassDoc[] the classes that extend this one
	 */
	public function getDescendants() {
		$descendants = array();
		foreach($this->namespace->classes as $class) {
			if ($class->extends == $this->name) {
				$descendants[] = $class;
			}
		}
		return $descendants;
	}
	
	/**
	 * Gets the documentation filename for this interface
	 * @return string the doc file name
	 */
	public function getDocFilename() {
		return $this->name."-class.html";
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
			// see if there is a constant or method on this class
			$all = $this->allMembers("constants");
			if (isset($all[$type])) {
				return CHtml::link($label,$all[$type]->docFilename."#".$type."-constant");
			}
			$all = $this->allMembers("properties");
			if (isset($all[$type])) {
				return CHtml::link($label,$all[$type]->docFilename."#".$type."-property");
			}
			
			$all = $this->allMembers("methods");
			if (isset($all[$type])) {
				return CHtml::link($label,$all[$type]->docFilename."#".$type."-method");
			}
		}
		else if ($type == $this->name) {
			// see if there is a constant or method on this class
			$all = $this->allMembers("constants");
			if (isset($all[$fragment])) {
				return CHtml::link($label,$all[$fragment]->docFilename."#".$fragment."-constant");
			}
			$all = $this->allMembers("properties");
			if (isset($all[$fragment])) {
				return CHtml::link($label,$all[$fragment]->docFilename."#".$fragment."-property");
			}
			$all = $this->allMembers("methods");
			if (isset($all[$fragment])) {
				return CHtml::link($label,$all[$fragment]->docFilename."#".$fragment."-method");
			}
			
			return $label;
		}
		return $this->namespace->typeLink($rawType,$label);
		
	}

	/**
	 * Parses the doc comment
	 */
	protected function parseDocComment() {
		if ($this->comment != "") {
			parent::parseDocComment();
			foreach($this->tags as $tag) {
				if ($tag->tagName == "property" && !isset($this->properties[$tag->name])) {
					$property = new AClassPropertyDoc;
					$property->name = $tag->name;
					$property->isPublic = true;
					$property->type = $tag->type;
					$property->comment = $tag->comment;
					$property->class = $this;
					$property->process();
					$this->properties[$tag->name] = $property;
				}
				 
			}
		}
	}
}
