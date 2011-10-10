<?php
Yii::import("packages.elasticSearch.*");

/**
 * Tests for the {@link AElasticSearchConnection} class
 *
 * @author Charles Pick
 * @package packages.elasticSearch.tests
 */
class AElasticSearchIndexTest extends CTestCase {
	/**
	 * tests the getIndices() method
	 */
	public function testIndices() {
		$indices = $this->getConnection()->getIndices();
		$this->assertTrue($indices instanceof CAttributeCollection);

	}

	/**
	 * Gets the elastic search connection
	 * @return AElasticSearchConnection the elastic search connection
	 */
	protected function getConnection() {
		return Yii::app()->elasticSearch;
	}
}