<?php
Yii::import("packages.stateMachine.*");
/**
 * Tests for the {@AStateMachine} class
 * @author Charles Pick
 * @package packages.stateMachine.tests
 */
class AStateMachineTest extends CTestCase {
	/**
	 * Tests the state machine magic methods
	 */
	public function testMagicMethods() {
		$machine = new AStateMachine();
		$machine->setStates(array(
							new ExampleEnabledState("enabled",$machine),
							new ExampleDisabledState("disabled",$machine),
							));
		$machine->defaultStateName = "enabled";
		$this->assertTrue($machine->is("enabled"));
		$this->assertFalse($machine->is("disabled"));
		$this->assertTrue($machine->isEnabled);
		$this->assertTrue(isset($machine->testProperty));
		$machine->disable();
		$this->assertTrue($machine->is("disabled"));
		$this->assertFalse($machine->is("enabled"));
		$this->assertFalse($machine->isEnabled);
		$this->assertFalse(isset($machine->testProperty));
	}

	/**
	 * Tests adding and removing states from a state machine
	 */
	public function testAddRemoveStates() {
		$machine = new AStateMachine();

		$machine->addState(new ExampleEnabledState("enabled",$machine));
		$this->assertFalse(isset($machine->testProperty));
		$machine->defaultStateName = "enabled";
		$this->assertTrue(isset($machine->testProperty));
		$machine->removeState("enabled");
		$this->assertFalse(isset($machine->testProperty));
		$this->assertNull($machine->getState());
	}

	/**
	 * Tests the transition events
	 */
	public function testTransitions() {
		$machine = new AStateMachine();
		$machine->setStates(array(
							new ExampleEnabledState("enabled",$machine),
							new ExampleDisabledState("disabled",$machine),
							new ExampleIntermediateState("intermediate", $machine),
							));
		$machine->defaultStateName = "enabled";
		$this->assertFalse($machine->transition("intermediate")); // intermediate state blocks transition from enabled -> intermediate
		$this->assertTrue($machine->transition("disabled"));
		$this->assertTrue($machine->transition("intermediate")); // should work

	}
	/**
	 * Tests for the behavior functionality
	 */
	public function testBehavior() {
		$machine = new AStateMachine();
		$machine->setStates(array(
							new ExampleEnabledState("enabled",$machine),
							new ExampleDisabledState("disabled",$machine),
							));
		$machine->defaultStateName = "enabled";

		$component = new CComponent();
		$component->attachBehavior("status",$machine);
		$this->assertTrue($component->is("enabled"));
		$this->assertTrue($component->transition("disabled"));
		$this->assertTrue($component->status->is("disabled"));
	}
}


/**
 * An example of an enabled state
 * @author Charles Pick
 * @package packages.stateMachine.tests
 */
class ExampleEnabledState extends AState {
	/**
	 * An example of a state property
	 * @var boolean
	 */
	public $isEnabled = true;

	/**
	 * An example of a state property
	 * @var boolean
	 */
	public $testProperty = true;

	/**
	 * Sets the state to disabled
	 */
	public function disable() {
		$this->_owner->transition("disabled");
	}
}
/**
 * An example of a disabled state
 * @author Charles Pick
 * @package packages.stateMachine.tests
 */
class ExampleDisabledState extends AState {
	/**
	 * An example of a state property
	 * @var boolean
	 */
	public $isEnabled = false;
	/**
	 * Sets the state to enabled
	 */
	public function enable() {
		$this->_owner->transition("enabled");
	}
}

/**
 * An example of an intermediate state
 * @author Charles Pick
 * @package packages.stateMachine.tests
 */
class ExampleIntermediateState extends AState {
	/**
	 * An example of a state property
	 * @var boolean
	 */
	public $isEnabled = null;

	/**
	 * Blocks the transition from enabled to intermediate
	 * @param AState $fromState the state we're transitioning from
	 * @return boolean whether the transition should continue
	 */
	public function beforeTransitionTo() {
		$fromState = $this->_owner->getState();
		if ($fromState->getName() == "enabled") {
			return false;
		}
		return parent::beforeTransitionTo();
	}
}

