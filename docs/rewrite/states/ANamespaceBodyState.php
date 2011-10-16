<?php
/**
 * A state that occurs when the token reader reaches a namespace body
 * @author Charles Pick
 * @package packages.docs.states
 */
class ANamespaceBodyState extends APHPTokenReaderState {

	/**
	 * Invoked after the state is transitioned from
	 */
	public function afterExit() {
		parent::afterExit();
		$to = $this->getOwner()->getState();
		if ($to->getName() == self::DEFAULT_STATE || $to->getName() == self::NAMESPACE_DECLARATION) {
			// this is the end of a namespace
			$this->getOwner()->getEntityStack()->pop(); // remove the namespace
		}


	}

}

