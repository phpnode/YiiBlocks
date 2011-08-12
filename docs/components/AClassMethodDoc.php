<?php
/**
 * Represents documentation information about a class method.
 * @author Charles Pick
 * @package packages.docs
 */
class AClassMethodDoc extends AFunctionDoc {
		
	/**
	 * The class this method belongs to
	 * @var AClassDoc
	 */
	public $class;
	
	/**
	 * Whether this method is abstract or not
	 * @var boolean
	 */
	public $isAbstract = false;
	
	/**
	 * Whether this method is final or not
	 * @var boolean
	 */
	public $isFinal = false;
	
	/**
	 * Whether this method is public or not
	 * @var boolean
	 */
	public $isPublic = false;
	
	/**
	 * Whether this method is protected or not
	 * @var boolean
	 */
	public $isProtected = false;
	
	/**
	 * Whether this method is private or not
	 * @var boolean
	 */
	public $isPrivate = false;
	
	/**
	 * Whether this method is static or not
	 * @var boolean
	 */
	public $isStatic = false;
	
	/**
	 * Gets the signature for this method
	 * @return string the signature to display for this method
	 */
	public function signature() {
		$signature = "";
		if ($this->isFinal) {
			$signature .= "final ";
		}
		
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
		$signature .= "function ".$this->name." ";
		$signature .= "(";
		$params = array();
		foreach($this->parameters as $parameter) {
			$params[] = $parameter->signature();
		}
		$signature .= implode(", ",$params);
		$signature .= ")";
		return $signature;
	}
	/**
	 * Gets the documentation filename for this method
	 * @return string the doc file name
	 */
	public function getDocFilename() {
		return $this->class->name.($this->class instanceof AClassDoc ? "-class" : "-interface").".html";
	}
	
}
