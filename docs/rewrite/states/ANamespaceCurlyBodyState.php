<?php
/**
 * A state that occurs when the token reader reaches a namespace body enclosed in curly brackets
 * @author Charles Pick
 * @package packages.docs.states
 */
class ANamespaceCurlyBodyState extends ANamespaceBodyState {
	/**
	 * Closes a set of curly brackets and transitions to the default state if appropriate
	 */
	public function closeCurlyBrackets() {
		parent::closeCurlyBrackets();
		if ($this->getOwner()->getCurlyBracketStack()->count() == 0) {
			$this->getOwner()->transition(self::DEFAULT_STATE);
		}
	}


}

