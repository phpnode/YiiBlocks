<?php
/**
 * Represents documentation information about a variable.
 * @author Charles Pick
 * @package packages.docs
 */
class AVariableDoc extends ATypeDoc {
	
	/**
	 * The type of variable
	 * @var ATypeDoc
	 */
	public $type;
	
	/**
	 * Whether this variable is global or not
	 * @var boolean
	 */
	public $isGlobal = false;
	
	/**
	 * Checks the documentation for this type
	 * @return boolean true if no errors were logged, otherwise false;
	 */
	public function check() {
		$hasErrors = false;
		if ($this->description == "") {
			$this->log("info","No description for variable");
			$hasErrors = true;
		}
		if ($this->type == "") {
			$this->log("info","No type specified for variable.");
			$hasErrors = true;
		}
		return !$hasErrors;
	}
	/**
	 * Parses the doc comment
	 */
	protected function parseDocComment() {
		if ($this->comment != "") {
			parent::parseDocComment();
			foreach($this->tags as $tag) {
				if ($tag->tagName == "var" && $this->type== "") {
					$this->type = $tag->type;
				}
			}
		}
	}
	
}
