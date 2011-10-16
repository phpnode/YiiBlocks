<?php
Yii::import("packages.fixedStack.AFixedStack");
/**
 * Tests for the {@link AFixedStack} class
 * @author Charles Pick
 * @package packages.fixedStack.tests
 */
class AFixedStackTest extends CTestCase {
	/**
	 * Ensures that a stack will not overflow its maximum size
	 */
	public function testFixedSize() {
		$stack = new AFixedStack(5);
		for($i = 0; $i <= 20; $i++) {
			$stack->push($i);
			if ($i < 5) {
				$this->assertEquals($i + 1, count($stack));
			}
			else {
				$this->assertEquals(5,count($stack));
			}
		}
	}
}