<?php
Yii::import("packages.docs.rewrite.models.*");
Yii::import("packages.docs.rewrite.states.*");
Yii::import("packages.docs.rewrite.*");
Yii::import("packages.stateMachine.*");
/**
 * Tests for the {@link APHPTokenReader}
 * @author Charles Pick
 * @package packages.docs.tests
 */
class APHPTokenReaderTest extends CTestCase {
	/**
	 * Tests the getCurlyBracketStack(), getSquareBracketStack(), getParenthesisStack() methods
	 */
	public function testStacks() {
		$tokens = token_get_all($this->getSourceString());
		$reader = new APHPTokenReader($tokens);
		$i = 0;
		while(($token = $reader->read()) !== false) {
			if ($i == 2) {
				$this->assertEquals(1,$reader->getParenthesisStack()->count());
				$this->assertEquals(2,$reader->currentLine);
			}
			elseif ($i == 8) {
				$this->assertEquals(0,$reader->getParenthesisStack()->count());
				$this->assertEquals(2,$reader->currentLine);
			}
			elseif ($i == 10) {
				$this->assertEquals(1,$reader->getCurlyBracketStack()->count());
				$this->assertEquals(2,$reader->currentLine);
			}
			elseif ($i == 27) {
				$this->assertEquals(0,$reader->getCurlyBracketStack()->count());
				$this->assertEquals(6,$reader->currentLine);
			}
			elseif ($i == 18) {
				$this->assertEquals(1,$reader->getSquareBracketStack()->count());
				$this->assertEquals(4,$reader->currentLine);
			}
			elseif ($i == 20) {
				$this->assertEquals(0,$reader->getSquareBracketStack()->count());
				$this->assertEquals(4,$reader->currentLine);
			}

			$i++;
		}
	}

	/**
	 * Gets the source code to use for this test
	 * @return string the test code
	 */
	protected function getSourceString() {
		return <<<PHP
<?php
if ("test" == "test") {
	doSomething();
	\$test["key"] = "blah";


}
PHP;

	}
}