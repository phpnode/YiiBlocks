<?php
/**
 * A state that occurs when the tokenizer reaches a value assignment
 * @author Charles Pick
 * @package packages.docs.states
 */
class AValueAssignmentState extends APHPTokenReaderState {
	/**
	 * Parses a given token
	 * @param array $token The token to parse
	 */
	public function parse($token) {

	}
	/**
	 * Invoked when the state is transitioned to. Clears the entity stack
	 * @param AState $from the state being transitioned from
	 */
	public function afterEnter(AState $from) {
		parent::afterEnter($from);
		$this->getOwner()->getStatementStack()->clear();
	}
	/**
	 * Triggered at the end of a php statement, e.g a semi colon
	 */
	public function endStatement() {
		$value = "";
		foreach($this->getOwner()->getStatementStack() as $token) {
			if (is_array($token)) {
				$value .= $token[1];
			}
			else {
				$value .= $token;
			}
		}
		$value = trim($value);
		$entity = $this->getOwner()->getEntityStack()->peek();
		$entity->value = $value;
		$entity->save();
		$stateHistory = $this->getOwner()->getTransitionHistory()->toArray();
		array_pop($stateHistory);
		$previousState = array_pop($stateHistory);
		if ($previousState == self::PROPERTY_DECLARATION) {
			$this->getOwner()->transition(self::CLASS_BODY);
		}
		else {
			$this->getOwner()->transition($previousState);
		}
		parent::endStatement();
	}
}