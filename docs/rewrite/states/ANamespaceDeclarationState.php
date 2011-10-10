<?php
/**
 * A state that occurs when the token reader reaches a namespace declaration
 * @author Charles Pick
 * @package packages.docs.states
 */
class ANamespaceDeclarationState extends APHPTokenReaderState {
	/**
	 * Holds the namespace
	 * @var ADocumentationNamespaceEntity
	 */
	protected $_namespace;
	/**
	 * Parses a given token
	 * @param array $token The token to parse
	 */
	public function parse($token) {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		switch ($token[0]) {
			case T_STRING:
				// this must be the namespace name
				$namespace = ADocumentationNamespaceEntity::model()->load($token[1],true);
				$this->setNamespace($namespace);
				break;
		}
	}
	/**
	 * Triggered at the end of a PHP statement
	 */
	public function endStatement() {
		parent::endStatement();
		if ($this->_namespace === null) {
			// this must be the global namespace
			$this->getOwner()->transition(APHPTokenReaderState::DEFAULT_STATE);
		}
		else {
			$this->getOwner()->getEntityStack()->push($this->_namespace);
			$this->getOwner()->transition(APHPTokenReaderState::NAMESPACE_BODY);
		}
	}

	public function openCurlyBrackets() {
		parent::openCurlyBrackets();
		if ($this->_namespace === null) {
			// this must be the global namespace
			$this->getOwner()->transition(APHPTokenReaderState::DEFAULT_STATE);
		}
		else {
			$this->_namespace->isCurly = true;
			$this->getOwner()->getEntityStack()->push($this->_namespace);
			$this->getOwner()->transition(APHPTokenReaderState::NAMESPACE_CURLY_BODY);
		}
	}

	/**
	 * Sets the namespace entity
	 * @param ADocumentationNamespaceEntity $namespace the namespace entity
	 */
	public function setNamespace($namespace) {
		$this->_namespace = $namespace;
	}

	/**
	 * Gets the namespace entity
	 * @return ADocumentationNamespaceEntity the namespace entity
	 */
	public function getNamespace() {
		return $this->_namespace;
	}


}