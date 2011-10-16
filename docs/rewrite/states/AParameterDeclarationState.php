<?php
/**
 * A state that occurs when the token reader reaches a method parameter declaration
 * @author Charles Pick
 * @package packages.docs.states
 */
class AMethodParameterDeclarationState extends APHPTokenReaderState {
	/**
	 * The type for this item
	 * @var string
	 */
	protected $_type;
	/**
	 * Parses a given token
	 * @param array $token The token to parse
	 */
	public function parse($token) {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		switch ($token[0]) {
			case T_STRING;
				// must be the type hint
				$this->_type = $token[1];
				break;
			case T_VARIABLE:
				$parameterName = substr($token[1],1);
				$method = $owner->getEntityStack()->peek(); /* @var ADocumentationMethodEntity $class */
				if (!isset($method->parameters->{$parameterName})) {
					$parameter = new ADocumentationParameterEntity();
					$parameter->name = $parameterName;
					$method->addChild($parameter);
				}
				else {
					$parameter = $method->parameters->{$parameterName};
				}

				break;
		}
	}
	/**
	 * Opens the parenthesis and transitions to the parameter declaration state
	 */
	public function closeParenthesis() {
		parent::closeParenthesis();
		if ($this->getOwner()->getParenthesisStack()->count() > 0) {
			return;
		}
		$this->getOwner()->transition(self::METHOD_DECLARATION);

	}
}