<?php
/**
 * A state that occurs when the token reader reaches a public class member
 * @author Charles Pick
 * @package packages.docs.states
 */
class APropertyDeclarationState extends AMemberDeclarationState {
	/**
	 * Triggered when the tokenizer reaches an assignment
	 */
	public function startAssignment() {
		$this->getOwner()->transition(self::VALUE_ASSIGNMENT);
	}
}