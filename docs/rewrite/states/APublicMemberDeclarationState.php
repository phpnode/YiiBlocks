<?php
/**
 * A state that occurs when the token reader reaches a public class member
 * @author Charles Pick
 * @package packages.docs.states
 */
class APublicMemberDeclarationState extends AMemberDeclarationState {

	/**
	 * Parses a given token
	 * @param array $token The token to parse
	 */
	public function parse($token) {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		switch ($token[0]) {

			case T_STATIC:
				$owner->transition(self::STATIC_MEMBER_DECLARATION);
				break;
			case T_FUNCTION:
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
				$property->isPublic = true;
				$property->save();

				$owner->transition(self::PROPERTY_DECLARATION);
				break;
		}
	}



}