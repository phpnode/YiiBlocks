<?php
/**
 * Represents documentation information about a function or method parmaeter
 * @author Charles Pick
 * @package packages.docs
 */
class AParameterDoc extends ATypeDoc {
		
	/**
	 * The type of this parameter
	 * @var ATypeDoc
	 */
	public $type;
	
	/**
	 * The default value for this parameter
	 * @var mixed
	 */
	public $value = "";
	
	/**
	 * The function this parameter belongs to
	 * @var AFunctionDoc
	 */
	public $function;
	
	/**
	 * Processes the parameter.
	 * @see ATypeDoc::process()
	 * @return boolean Whether this was processed or not
	 */
	public function process() {
		$this->value = trim($this->value);
		return parent::process();
	}
	
	/**
	 * Gets the signature for this parameter
	 * @return string the signature to display for this parameter
	 */
	public function signature() {
		$signature = "";
		
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
	 * Checks the documentation for this type
	 * @return boolean true if no errors were logged, otherwise false;
	 */
	public function check() {
		$hasErrors = !parent::check();
		if ($this->type == "") {
			$this->log("warning","No type set for parameter: $this->name");
		}
		
	
		return !$hasErrors;
	}
}
