<?php
/**
 * Represents documentation information about a constant.
 * @author Charles Pick
 * @package packages.docs
 */
class AClassConstantDoc extends AConstantDoc {
	
	/**
	 * The class this constant belongs to
	 * @var AClassDoc
	 */
	public $class;
	
	/**
	 * Gets the documentation filename for this constant
	 * @return string the doc file name
	 */
	public function getDocFilename() {
		return $this->class->name.($this->class instanceof AClassDoc ? "-class" : "-interface").".html";
	}

}
