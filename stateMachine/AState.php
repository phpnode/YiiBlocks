<?php
/**
 * Represents a state for a state machine
 *
 * @author Charles Pick
 * @package packages.stateMachine
 */
class AState extends CComponent {
	/**
	 * The state machine this state belongs to
	 * @var AStateMachine
	 */
	protected $_owner;

	/**
	 * The name of this state
	 * @var string
	 */
	protected $_name;

	/**
	 * Constructor
	 * @param string $name The name of the state
	 * @param AStateMachine $owner the state machine this state belongs to
	 */
	public function __construct($name, AStateMachine $owner) {
		$this->setName($name);
		$this->setOwner($owner);
	}

	/**
	 * Invoked before the state is transitioned to
	 * @return boolean true if the event is valid and the transition should be allowed to continue
	 */
	public function beforeEnter() {
		$transition = new AStateTransition($this);
		$transition->to = $this;
		$transition->from = $this->_owner->getState();
		$this->onBeforeEnter($transition);
		return $transition->isValid;
	}
	/**
	 * This event is raised before the state is transitioned to
	 * @param AStateTransition $transition the state transition
	 */
	public function onBeforeEnter($transition) {
		$this->raiseEvent("onBeforeEnter",$transition);
	}

	/**
	 * Invoked after the state is transitioned to
	 * @param AState $from The state we're transitioning from
	 */
	public function afterEnter(AState $from) {
		$transition = new AStateTransition($this);
		$transition->to = $this;
		$transition->from = $from;
		$this->onAfterEnter($transition);
	}
	/**
	 * This event is raised after the state is transitioned to
	 * @param AStateTransition $transition the state transition
	 */
	public function onAfterEnter($transition) {
		$this->raiseEvent("onAfterEnter",$transition);
	}
	/**
	 * Invoked before the state is transitioned from
	 * @param AState $toState The state we're transitioning to
	 * @return boolean true if the event is valid and the transition should be allowed to continue
	 */
	public function beforeExit(AState $toState) {
		$transition = new AStateTransition($this);
		$transition->to = $toState;
		$transition->from = $this;
		$this->onBeforeExit($transition);
		return $transition->isValid;
	}
	/**
	 * This event is raised before the state is transitioned from
	 * @param AStateTransition $transition the state transition
	 */
	public function onBeforeExit($transition) {
		$this->raiseEvent("onBeforeExit",$transition);
	}

	/**
	 * Invoked after the state is transitioned from
	 */
	public function afterExit() {
		$transition = new AStateTransition($this);
		$transition->from = $this;
		$transition->to = $this->_owner->getState();
		$this->onAfterExit($transition);
	}
	/**
	 * This event is raised after the state is transitioned from
	 * @param AStateTransition $transition the state transition
	 */
	public function onAfterExit($transition) {
		$this->raiseEvent("onAfterExit",$transition);
	}

	/**
	 * Sets the name for this state
	 * @param string $name
	 */
	public function setName($name) {
		return $this->_name = $name;
	}

	/**
	 * Gets the name for this state
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Sets the state machine that this state belongs to
	 * @param AStateMachine $owner the state machine this state belongs to
	 * @return AStateMachine the state machine
	 */
	public function setOwner($owner) {
		return $this->_owner = $owner;
	}

	/**
	 * Gets the state machine the state belongs to
	 * @return AStateMachine
	 */
	public function getOwner() {
		return $this->_owner;
	}
}