<?php
/**
 * Implements a simple state machine.
 * State machines can have various states, each state can provide methods and properties unique to that state
 * The state machine also manages the transitions between states.
 * Since AStateMachine extends CBehavior, it can also be attached to other models
 * e.g.
 * <pre>
 * 	$stateMachine = new AStateMachine;
 *  $stateMachine->setStates(array(
 *	 	new ExampleEnabledState("enabled",$stateMachine),
 *      new ExampleDisabledState("disabled",$stateMachine),
 * ));
 * $stateMachine->defaultStateName = "enabled";
 * $model = new User;
 * $model->attachBehavior("status", $stateMachine);
 * echo $model->status->is("enabled") ? "true" : "false"; // "true"
 * $model->transition("disabled");
 * echo $model->status->getState(); // "disabled"
 * $model->status->enable(); // assuming enable is a method on ExampleDisabledState
 * echo $model->status->getState(); // "enabled"
 * </pre>
 *
 *
 * @author Charles Pick
 * @package packages.stateMachine
 */
class AStateMachine extends CBehavior implements IApplicationComponent {
	/**
	 * Holds the name of the default state
	 * @var string
	 */
	public $defaultStateName;

	/**
	 * The name of the current state
	 * @var string
	 */
	protected $_stateName;
	/**
	 * The supported states
	 * @var AState[]
	 */
	protected $_states = array();
	/**
	 * Whether the state machine is initialized or not
	 * @var boolean
	 */
	protected $_isInitialized = false;

	/**
	 * Constructor.
	 * The default implementation calls the init() method
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initializes the state machine.
	 * The default implementation merely sets the $this->_isInitialized property to true
	 * Child classes that override this method should call the parent implementation
	 * This method is required by IApplicationComponent
	 */
	public function init() {
		$this->_isInitialized = true;
	}
	/**
	 * Determines whether the state machine has been initialized or not
	 * @return boolean
	 */
	public function getIsInitialized() {
		return $this->_isInitialized;
	}
	/**
	 * Sets the possible states for this machine
	 * @param AState[] $states an array of possible states
	 */
	public function setStates($states) {
		$this->_states = array();
		foreach($states as $state) {
			$this->addState($state);
		}
		return $this->_states;
	}
	/**
	 * Gets an array of possible states for this machine
	 * @return AState[] the possible states for the machine
	 */
	public function getStates() {
		return $this->_states;
	}

	/**
	 * Adds a state to the machine
	 * @param AState|array $state The state to add, either an instance of AState or a configuration array for an AState
	 * @return AState the added state
	 */
	public function addState($state) {
		if (is_array($state)) {
			if (!isset($state['class'])) {
				$state['class'] = "AState";
			}
			$state = Yii::createComponent($state,$this);
		}
		return $this->_states[$state->getName()] = $state;
	}
	/**
	 * Removes a state with the given name
	 * @param string $stateName the name of the state to remove
	 * @return AState|null the removed state, or null if there was no state by that name
	 */
	public function removeState($stateName) {
		if (!$this->hasState($stateName)) {
			return null;
		}
		$state = $this->_states[$stateName];
		unset($this->_states[$stateName]);
		$this->_stateName = $this->defaultStateName;
		return $state;
	}
	/**
	 * Sets the name of the current state but doesn't trigger the transition events
	 * @param string $state the name of the state to change to
	 */
	public function setStateName($state) {
		$this->_stateName = $state;
	}

	/**
	 * Gets the name of the current state
	 * @return string
	 */
	public function getStateName() {
		if ($this->_stateName === null) {
			return $this->defaultStateName;
		}
		return $this->_stateName;
	}
	/**
	 * Gets the default state
	 * @return AState|null the default state, or null if no state is set
	 */
	public function getDefaultState() {
		if (is_null($this->defaultStateName) || !$this->hasState($this->defaultStateName)) {
			return null;
		}
		return $this->_states[$this->defaultStateName];
	}

	/**
	 * Gets the current state
	 * @return AState|null the current state, or null if there is no state set
	 */
	public function getState() {
		$stateName = $this->getStateName();
		if (!isset($this->_states[$stateName])) {
			return null;
		}
		return $this->_states[$stateName];
	}
	/**
	 * Transitions to a
	 * @param string $state the name of the state
	 * @return boolean true if the state exists, otherwise false
	 */
	public function hasState($state) {
		return isset($this->_states[$state]);
	}

	/**
	 * Transitions the state machine to the specified state
	 * @throws AInvalidStateException if the state doesn't exist
	 * @param string $to The name of the state we're transitioning to
	 * @return boolean true if the transition succeeded or false if it failed
	 */
	public function transition($to) {
		if (!$this->hasState($to)) {
			throw new AInvalidStateException("No such state: ".$to);
		}
		$toState = $this->_states[$to];
		$fromState = $this->getState();
		if (!$this->beforeTransition($toState)) {
			return false;
		}
		$this->setStateName($to);
		$this->afterTransition($fromState);
		return true;
	}

	/**
	 * Invoked before a state transition
	 * @param AState $toState The state we're transitioning to
	 * @return boolean true if the event is valid and the transition should be allowed to continue
	 */
	public function beforeTransition(AState $toState) {
		if (!$this->getState()->beforeTransitionFrom($toState) || !$toState->beforeTransitionTo()) {
			return false;
		}
		$transition = new AStateTransition($this);
		$transition->to = $toState;
		$transition->from = $this->getState();
		$this->onBeforeTransition($transition);
		return $transition->isValid;
	}
	/**
	 * This event is raised before a state transition
	 * @param AStateTransition $transition the state transition
	 */
	public function onBeforeTransition($transition) {
		$this->raiseEvent("onBeforeTransition",$transition);
	}

	/**
	 * Invoked after a state transition
	 * @param AState $from The state we're transitioning from
	 */
	public function afterTransition(AState $fromState) {
		$fromState->afterTransitionFrom();
		$this->getState()->afterTransitionTo($fromState);

		$transition = new AStateTransition($this);
		$transition->to = $this->getState();
		$transition->from = $fromState;
		$this->onAfterTransition($transition);
	}
	/**
	 * This event is raised after a state transition
	 * @param AStateTransition $transition the state transition
	 */
	public function onAfterTransition($transition) {
		$this->raiseEvent("onAfterTransitionTo",$transition);
	}
	/**
	 * Returns a property value based on its name.
	 * @param string $name the property name or event name
	 * @return mixed the property value, event handlers attached to the event, or the named behavior (since version 1.0.2)
	 * @throws CException if the property or event is not defined
	 * @see CComponent::__get()
	 */
	public function __get($name) {
		$state = $this->getState();
		if ($state !== null && (property_exists($state,$name) || $state->canGetProperty($name))) {
			return $state->{$name};
		}
		return parent::__get($name);
	}

	/**
	 * Sets a property value based on its name.
	 * @param string $name the property name or event name
	 * @param mixed $value the property value
	 * @return mixed the property value, event handlers attached to the event, or the named behavior (since version 1.0.2)
	 * @throws CException if the property or event is not defined
	 * @see CComponent::__get()
	 */
	public function __set($name,$value) {
		$state = $this->getState();
		if ($state !== null && (property_exists($state,$name) || $state->canSetProperty($name))) {
			return $state->{$name} = $value;
		}
		return parent::__set($name,$value);
	}

	/**
	 * Checks if a property value is null.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using isset() to detect if a component property is set or not.
	 * @param string $name the property name or the event name
	 * @return boolean
	 * @since 1.0.1
	 */
	public function __isset($name) {
		$state = $this->getState();
		if ($state !== null && (property_exists($state,$name) || $state->canGetProperty($name))) {
			return true;
		}
		return parent::__isset($name);
	}


	/**
	 * Sets a component property to be null.
	 * @param string $name the property name or event name
	 * @return mixed the property value, event handlers attached to the event, or the named behavior (since version 1.0.2)
	 * @throws CException if the property or event is not defined
	 * @see CComponent::__get()
	 */
	public function __unset($name) {
		$state = $this->getState();
		if ($state !== null && (property_exists($state,$name) || $state->canSetProperty($name))) {
			return $state->{$name} = null;
		}
		return parent::__unset($name);
	}


	/**
	 * Calls the named method which is not a class method.
	 * Do not call this method. This is a PHP magic method that we override
	 * to implement the states feature.
	 * @param string $name the method name
	 * @param array $parameters method parameters
	 * @return mixed the method return value
	 */
	public function __call($name,$parameters) {
		$state = $this->getState();
		if (is_object($state) && method_exists($state,$name)) {
			return call_user_func_array(array($state,$name),$parameters);
		}
		return parent::__call($name,$parameters);
	}


	/**
	 * Determines whether the current state matches the given name
	 * @param string $stateName the name of the state to check against
	 * @return boolean true if the state names match
	 */
	public function is($stateName) {
		return $this->getStateName() == $stateName;
	}
}