<?php
Yii::import("packages.docs.rewrite.models.*");
Yii::import("packages.docs.rewrite.states.*");
Yii::import("packages.docs.rewrite.*");
Yii::import("packages.stateMachine.*");
class AClassDeclarationStateTest extends CTestCase {
	public function testParse() {
		$code = $this->getSourceString();
		$reader = new APHPTokenReader(token_get_all($code));
		$curlyBrackets = new CStack();
		$lastToken = null;
		while(($token = $reader->read()) !== false) {

			if (is_array($token)) {
				echo token_name($token[0])." - ".$token[1]."\n";
				$lastToken = $token;

				switch ($token[0]) {
					case T_CLASS:
						$this->assertEquals(APHPTokenReaderState::CLASS_DECLARATION,$reader->getState()->getName());
						break;
					case T_PUBLIC:
						$this->assertEquals(APHPTokenReaderState::PUBLIC_MEMBER_DECLARATION,$reader->getState()->getName());
						break;
				}
			}
			else {
				echo $token."\n";
				switch ($token) {

				}
			}
		}
	}
	/**
	 * Gets the source code to use for this test
	 * @return string the test code
	 */
	protected function getSourceString() {
		return <<<PHP
<?php
class testClass {
	public \$publicProperty = "test";

}
namespace testNamespace2 {
	class testClass {

	}
}

PHP;

	}
}