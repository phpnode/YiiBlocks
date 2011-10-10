<?php
/**
 * A state that occurs when the token reader reaches a class declaration
 * @author Charles Pick
 * @package packages.docs.states
 */
class AClassDeclarationState extends APHPTokenReaderState {
	/**
	 * Holds the class
	 * @var ADocumentationClassEntity
	 */
	protected $_class;
	/**
	 * Parses a given token
	 * @param array $token The token to parse
	 */
	public function parse($token) {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		switch ($token[0]) {
			case T_STRING:
				// this must be the class name
				$className = $token[1];
				if ($this->getOwner()->getEntityStack()->count() == 1) {
					$className = $this->getOwner()->getEntityStack()->peek()->name."\\".$className;
				}
				$class = ADocumentationClassEntity::model()->load($className,true);
				$this->setClass($class);
				$this->getOwner()->getEntityStack()->push($class);
				break;
			case T_EXTENDS:
				$this->getOwner()->transition(self::EXTENDS_DECLARATION);
				break;
			case T_IMPLEMENTS:
				$this->getOwner()->transition(self::IMPLEMENTS_DECLARATION);
				break;
		}
	}
	/**
	 * Opens a set of curly brackets and transitions to the
	 */
	public function openCurlyBrackets() {
		parent::openCurlyBrackets();

		$this->getOwner()->transition(APHPTokenReaderState::CLASS_BODY);

	}

	/**
	 * Sets the class entity
	 * @param ADocumentationClassEntity $class the class entity
	 */
	public function setClass($class) {
		$this->_class = $class;
	}

	/**
	 * Gets the class entity
	 * @return ADocumentationClassEntity the class entity
	 */
	public function getClass() {
		return $this->_class;
	}


}