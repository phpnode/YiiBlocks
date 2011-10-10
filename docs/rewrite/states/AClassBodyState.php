<?php
/**
 * A state that occurs when the token reader reaches a class body
 * @author Charles Pick
 * @package packages.docs.states
 */
class AClassBodyState extends APHPTokenReaderState {

	/**
	 * Parses a given token
	 * @param array $token The token to parse
	 */
	public function parse($token) {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		switch ($token[0]) {
			case T_CONST:
				$owner->transition(self::CONST_MEMBER_DECLARATION);
				break;
			case T_PUBLIC:
				$owner->transition(self::PUBLIC_MEMBER_DECLARATION);
				break;
			case T_PROTECTED:
				$owner->transition(self::PROTECTED_MEMBER_DECLARATION);
				break;
			case T_PRIVATE:
				$owner->transition(self::PRIVATE_MEMBER_DECLARATION);
				break;
			case T_STATIC:
				$owner->transition(self::STATIC_MEMBER_DECLARATION);
				break;
			case T_FINAL:
				$owner->transition(self::FINAL_MEMBER_DECLARATION);
				break;
			case T_ABSTRACT:
				$owner->transition(self::ABSTRACT_MEMBER_DECLARATION);
				break;
		}
	}
	/**
	 * Closes a set of curly brackets and ends the class body
	 * @see APHPTokenReaderState::closeCurlyBrackets()
	 */
	public function closeCurlyBrackets() {
		parent::closeCurlyBrackets();
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		$entityStack = $owner->getEntityStack();
		$entityStack->pop(); // remove the class
		if ($entityStack->count() == 0) {
			$this->getOwner()->transition(APHPTokenReaderState::DEFAULT_STATE);
		}
		else {
			if ($entityStack->peek()->isCurly) {
				$this->getOwner()->transition(APHPTokenReaderState::NAMESPACE_CURLY_BODY);
			}
			else {
				$this->getOwner()->transition(APHPTokenReaderState::NAMESPACE_BODY);
			}
		}

	}

}