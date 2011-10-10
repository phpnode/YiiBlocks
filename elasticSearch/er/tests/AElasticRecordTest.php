<?php
Yii::import("packages.elasticSearch.*");
Yii::import("packages.elasticSearch.er.*");
/**
 * Tests for the {@link AElasticRecord} class
 */
class AElasticRecordTest extends CTestCase {
	/**
	 * Tests the basic functionality
	 */
	public function testBasics() {
		$model = new AElasticRecord;
		$model->id = 123;
		$model->name = "test item";
		$this->assertEquals(123, $model->id);
		$this->assertEquals("test item",$model->name);
	}
}