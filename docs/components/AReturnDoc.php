<?php
/**
 * Represents documentation information about whatever is returned from a particular method or function.
 * @author Charles Pick
 * @package packages.docs
 */
class AReturnDoc extends ATypeDoc {
	/**
	 * The function this statement belongs to
	 * @var AFunctionDoc
	 */
	public $function;
	
	/**
	 * The type of data returned by the function
	 * @var ATypeDoc
	 */
	public $type;
	
	/**
	 * Checks the documentation for this type
	 * @return boolean true if no errors were logged, otherwise false;
	 */
	public function check() {
		$hasErrors = false;
		if ($this->description == "") {
			$this->log("info","No description for return statement");
			$hasErrors = true;
		}
		if ($this->type == "") {
			$this->log("info","No type specified for return statement.");
			$hasErrors = true;
		}
		return !$hasErrors;
	}
}
