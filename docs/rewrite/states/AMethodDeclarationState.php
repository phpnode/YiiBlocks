<?php
/**
 * A state that occurs when the token reader reaches a class method declaration
 * @author Charles Pick
 * @package packages.docs.states
 */
class AMethodDeclarationState extends AMemberDeclarationState {
	/**
	 * Parses a given token
	 * @param array $token The token to parse
	 */
	public function parse($token) {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		switch ($token[0]) {
			case T_STRING;
				// must be the method name
				$methodName = substr($token[1],1);
				$m = $owner->getEntityStack()->pop(); /* @var ADocumentationMethodEntity $m */
				$class = $owner->getEntityStack()->peek(); /* @var ADocumentationClassEntity $class */
				if (!isset($class->methods->{$methodName})) {
					$method = new ADocumentationMethodEntity();
					$method->name = $methodName;
					$class->addChild($method);
				}
				else {
					$method = $class->methods->{$methodName};
				}

				$method->isPublic = $m->isPublic;
				$method->isPrivate = $m->isPrivate;
				$method->isProtected = $m->isProtected;
				$method->isStatic = $m->isStatic;
				$method->save();
				$owner->getEntityStack()->push($method);
				break;
		}
	}
	/**
	 * Opens the parenthesis and transitions to the parameter declaration state
	 */
	public function openParenthesis() {
		parent::openParenthesis();
		$this->getOwner()->transition(self::PARAMETER_DECLARATION);
	}
}