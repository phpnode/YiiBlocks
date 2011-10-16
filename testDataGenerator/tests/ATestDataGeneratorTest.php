<?php
Yii::import("packages.testDataGenerator.*");
/**
 * Tests for the {@link ATestDataGenerator} class
 * @author Charles Pick
 * @package packages.testDataGenerator.tests
 */
class ATestDataGeneratorTest extends CTestCase {
	/**
	 * Tests the generateUserDetails() method
	 */
	public function testGenerateUserDetails() {
		$gen = new ATestDataGenerator();
		$gen->generateUserDetails();
		$this->insertTestData();
	}

	public function insertTestData() {
		$gen = new ATestDataGenerator();
		$db = $gen->getDb();
		$file = dirname(__FILE__)."/../part-of-speech.txt";
		$handle = fopen($file,"r");
		while (($buffer = fgets($handle, 4096)) !== false) {
			if (strstr($buffer,"'")) {
				continue;
			}
			$line = explode("\t",$buffer);
			$word = array_shift($line);
			echo $word."\n";
			$parts = array_shift($line);
			$attributes = array(
				"word" => $word,
			);
			if (strstr($parts,"N")) {
				$attributes['isNoun'] = true;
			}
			if (strstr($parts,"P")) {
				$attributes['isPlural'] = true;
			}
			if (strstr($parts,"h")) {
				$attributes['isNounPhrase'] = true;
			}
			if (strstr($parts,"V")) {
				$attributes['isVerb'] = true;
			}
			if (strstr($parts,"t")) {
				$attributes['isTransitiveVerb'] = true;
			}
			if (strstr($parts,"i")) {
				$attributes['isIntransitiveVerb'] = true;
			}
			if (strstr($parts,"A")) {
				$attributes['isAdjective'] = true;
			}
			if (strstr($parts,"v")) {
				$attributes['isAdverb'] = true;
			}
			if (strstr($parts,"C")) {
				$attributes['isConjunction'] = true;
			}
			if (strstr($parts,"!")) {
				$attributes['isInterjection'] = true;
			}
			if (strstr($parts,"r")) {
				$attributes['isPronoun'] = true;
			}
			if (strstr($parts,"D")) {
				$attributes['isDefiniteArticle'] = true;
			}
			if (strstr($parts,"I")) {
				$attributes['isIndefiniteArticle'] = true;
			}
			if (strstr($parts,"o")) {
				$attributes['isNominative'] = true;
			}
			$params = array();
			foreach($attributes as $attribute => $value) {
				$params[] = ":".$attribute;
			}
			$command = $db->createCommand("INSERT INTO words (".implode(", ",array_keys($attributes)).") VALUES (".implode(", ",$params).")");
			echo $command->text."\n";
			foreach($attributes as $attribute => $value) {
				$command->bindValue(":".$attribute,$value);
			}
			$command->execute();
		}
		fclose($handle);
	}
}