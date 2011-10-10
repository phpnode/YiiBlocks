<?php
Yii::import("packages.elasticSearch.*");

/**
 * Tests for the {@link AElasticSearchDocument} class
 *
 * @author Charles Pick
 * @package packages.elasticSearch.tests
 */
class AElasticSearchDocumentTest extends CTestCase {
	/**
	 * tests the attributeNames(), setAttributes() methods
	 */
	public function testAttributes() {
		$document = new AElasticSearchDocument();
		$this->assertEquals(array(),$document->attributeNames());
		$attributes = array(
			"name" => "Test Document",
			"email" => "email@email.com",
		);
		$document->setAttributes($attributes);
		$this->assertEquals("Test Document", $document->name);
		$this->assertEquals("email@email.com", $document->email);
		$this->assertTrue($document->getIsNewRecord());
	}

	/**
	 * Gets the elastic search connection
	 * @return AElasticSearchConnection the elastic search connection
	 */
	protected function getConnection() {
		return Yii::app()->elasticSearch;
	}
}