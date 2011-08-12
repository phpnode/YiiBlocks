<?php
/**
 * Represents documentation information about a constant.
 * @author Charles Pick
 * @package packages.docs
 */
class AConstantDoc extends ATypeDoc {
	
	/**
	 * The value of the constant
	 * @var mixed
	 */
	public $value;
	
		/**
	 * Gets the signature for this constant
	 * @return string the signature to display for this constant
	 */
	public function signature() {
		return "const ".$this->name." = ".$this->value;
	}
	
}
