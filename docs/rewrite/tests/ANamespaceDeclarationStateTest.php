<?php
Yii::import("packages.docs.rewrite.models.*");
Yii::import("packages.docs.rewrite.states.*");
Yii::import("packages.docs.rewrite.*");
Yii::import("packages.stateMachine.*");
class ANamespaceDeclarationStateTest extends CTestCase {
	public function testParse() {
		$code = $this->getSourceString();
		$reader = new APHPTokenReader(token_get_all($code));
		$curlyBrackets = new CStack();
		$lastToken = null;
		while(($token = $reader->read()) !== false) {
			if (is_array($token)) {
				$lastToken = $token;
				switch ($token[0]) {
					case T_NAMESPACE:
						$this->assertEquals(APHPTokenReaderState::NAMESPACE_DECLARATION,$reader->getState()->getName());
						break;
					case T_CLASS:
						$this->assertEquals(APHPTokenReaderState::CLASS_DECLARATION,$reader->getState()->getName());
						break;
				}
			}
			else {
				switch ($token) {
					case "{":
						$curlyBrackets->push($lastToken);
						$this->assertEquals($curlyBrackets->count(), $reader->getCurlyBracketStack()->count());
						if ($curlyBrackets->count() == 1) {
							$this->assertEquals(APHPTokenReaderState::NAMESPACE_CURLY_BODY,$reader->getState()->getName());
						}
						break;
					case "}":
						$l = $curlyBrackets->pop();
						#echo $l[2]." to ".$reader->currentLine."\n";
						$this->assertEquals($curlyBrackets->count(), $reader->getCurlyBracketStack()->count());
						if ($curlyBrackets->count() == 0) {
							$this->assertEquals(APHPTokenReaderState::DEFAULT_STATE,$reader->getState()->getName());
						}
						break;
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
namespace testNamespace {
	class testClass {

	}
	class testClass2 {

	}
}

namespace testNamespace2 {
	class testClass {

	}
}

PHP;

	}
}