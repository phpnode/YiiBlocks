<?php
/**
 * A base class for member declarations
 * @author Charles Pick
 * @package packages.docs.states
 */
class AMemberDeclarationState extends APHPTokenReaderState {
	/**
	 * Whether the member is abstract or not
	 * @var boolean
	 */
	public $isAbstract = false;

	/**
	 * Whether the member is final or not
	 * @var boolean
	 */
	public $isFinal = false;

	/**
	 * Whether the member is static or not
	 * @var boolean
	 */
	public $isStatic = false;

	/**
	 * Whether the member is public or not
	 * @var boolean
	 */
	public $isPublic = false;

	/**
	 * Whether the member is protected or not
	 * @var boolean
	 */
	public $isProtected = false;

	/**
	 * Whether the member is private or not
	 * @var boolean
	 */
	public $isPrivate = false;

	/**
	 * Parses a given token
	 * @param array $token The token to parse
	 */
	public function parse($token) {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		switch ($token[0]) {
			case T_PUBLIC:
				$this->isPublic = true;
				break;
			case T_PRIVATE:
				$this->isPrivate = true;
				break;
			case T_PROTECTED:
				$this->isProtected = true;
				break;
			case T_ABSTRACT:
				$this->isAbstract = true;
				break;
			case T_FINAL:
				$this->isFinal = true;
				break;
			case T_STATIC:
				$this->isStatic = true;
				break;
			case T_FUNCTION:
				$method = new ADocumentationMethodEntity();
				$method->isPublic = $this->isPublic;
				$method->isPrivate = $this->isPrivate;
				$method->isProtected = $this->isProtected;
				$method->isStatic = $this->isStatic;
				$this->getOwner()->getEntityStack()->push($method);
				$owner->transition(self::METHOD_DECLARATION);
				break;
			case T_VARIABLE;
				$propertyName = substr($token[1],1);
				$class = $owner->getEntityStack()->peek(); /* @var ADocumentationClassEntity $class */
				if (!isset($class->properties->{$propertyName})) {
					$property = new ADocumentationPropertyEntity();
					$property->name = $propertyName;
					$class->addChild($property);
				}
				else {
					$property = $class->properties->{$propertyName};
				}
				$property->isPublic = $this->isPublic;
				$property->isPrivate = $this->isPrivate;
				$property->isProtected = $this->isProtected;
				$property->isStatic = $this->isStatic;
				$property->save();

				$owner->transition(self::PROPERTY_DECLARATION);
				break;
		}
	}
	/**
	 * Triggered at the end of a statement
	 */
	public function endStatement() {
		parent::endStatement();
		$this->getOwner()->transition(self::CLASS_BODY);
	}
	/**
	 * Resets the state
	 */
	public function afterExit() {
		parent::afterExit();
		$this->isAbstract = false;
		$this->isFinal = false;
		$this->isPrivate = false;
		$this->isProtected = false;
		$this->isPublic = false;
		$this->isStatic = false;
	}
}