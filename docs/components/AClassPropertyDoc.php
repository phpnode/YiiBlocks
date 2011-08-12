<?php
/**
 * Represents documentation information about a class property.
 * @author Charles Pick
 * @package packages.docs
 */
class AClassPropertyDoc extends AVariableDoc {
		
	/**
	 * The class this property belongs to
	 * @var AClassDoc
	 */
	public $class;
	
	/**
	 * The value for this property
	 * @var string
	 */
	public $value = "";
	
	/**
	 * Whether this property is public or not
	 * @var boolean
	 */
	public $isPublic = false;
	
	/**
	 * Whether this property is protected or not
	 * @var boolean
	 */
	public $isProtected = false;
	
	/**
	 * Whether this property is private or not
	 * @var boolean
	 */
	public $isPrivate = false;
	
	/**
	 * Whether this property is static or not
	 * @var boolean
	 */
	public $isStatic = false;
	
	/**
	 * Processes the property.
	 * @see ATypeDoc::process()
	 * @return boolean Whether this was processed or not
	 */
	public function process() {
		$this->value = trim($this->value);
		return parent::process();
	}
	
	/**
	 * Gets the signature for this property
	 * @return string the signature to display for this property
	 */
	public function signature() {
		$signature = "";
		if ($this->isPublic) {
			$signature .= "public ";
		}
		elseif ($this->isPrivate) {
			$signature .= "private ";
		}
		elseif ($this->isProtected) {
			$signature .= "protected ";
		}
		
		if ($this->isStatic) {
			$signature .= "static ";
		}
		if ($this->type !== null) {
			$signature .= $this->type." ";
		}
		$signature .= '$'.$this->name;
		if ($this->value != "") {
			$signature .= " = ".$this->value;
		}
		return $signature;
	}
	/**
	 * Gets the documentation filename for this property
	 * @return string the doc file name
	 */
	public function getDocFilename() {
		return $this->class->name.($this->class instanceof AClassDoc ? "-class" : "-interface").".html";
	}
}
