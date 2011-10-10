<?php
Yii::import("packages.docs.rewrite.models.*");
/**
 * Tests for the {@link ADocumentationEntity} class
 * @author Charles Pick
 * @package packages.docs.tests
 */
class ADocumentationEntityTest extends CTestCase {
	/**
	 * Tests the beforeSave(), afterSave() and afterDelete() methods
	 */
	public function testEvents() {
		$model = new ADocumentationEntity();
		$model->name = "test entity";
		$this->assertTrue($model->save());
		$this->assertTrue($model->timeAdded > 0);
		$this->assertTrue(($model->hierarchy instanceof ADocumentationHierarchy));
		$hierarchyId = $model->hierarchy->id;
		$model->delete(); // clean up
		$this->assertNull(ADocumentationHierarchy::model()->findByPk($hierarchyId));
	}
	/**
	 * Tests the getChildren(), addChild() methods
	 */
	public function testChildren() {
		$model = new ADocumentationEntity();
		$model->name = "test parent";
		$this->assertTrue($model->save());
		for($i = 0; $i <= 5; $i++) {
			$child = new ADocumentationEntity();
			$child->name = "test child $i";
			$this->assertTrue($model->addChild($child));
			$this->assertFalse($model->addChild($child));
			$this->assertTrue($child->hierarchy->isDescendantOf($model->hierarchy));

		}
		$model = ADocumentationEntity::model()->findByPk($model->id);
		$childIds = array();
		foreach($model->getChildren() as $i => $child) {
			$this->assertEquals("test child $i",$child->name);
			$childIds[] = $child->id;
		}
		$this->assertTrue(is_object(ADocumentationEntity::model()->byName("test parent")->find()));
		$this->assertTrue(is_object(ADocumentationEntity::model()->byName("\\test parent\\test child 1")->find()));
		$model->delete();
		$criteria = new CDbCriteria();
		$criteria->addInCondition("t.id", $childIds);
		$this->assertEquals(0, ADocumentationEntity::model()->count($criteria));
	}
}