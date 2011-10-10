<?php
Yii::import("packages.docs.rewrite.models.*");
/**
 * Tests for the {@link ADocumentationClassEntity} class
 * @author Charles Pick
 * @package packages.docs.tests
 */
class ADocumentationClassEntityTest extends CTestCase {

	/**
	 * Tests the getConstants(), getProperties(), getMethods()
	 */
	public function testChildren() {
		$model = new ADocumentationClassEntity();
		$model->name = "TestClass";
		$this->assertTrue($model->save());
		$const = new ADocumentationConstEntity();
		$const->name = "TestConst";
		$const->value = "'hello world'";
		$model->addChild($const);
		$property = new ADocumentationPropertyEntity();
		$property->name = "TestProperty";
		$property->value = "'hello world'";
		$model->addChild($property);
		$model = ADocumentationClassEntity::model()->findByPk($model->id);
		$this->assertTrue(is_object($model));
		$this->assertEquals(1,count($model->getConstants()));
		$this->assertEquals("TestConst",$model->constants[0]->name);
		$this->assertEquals("TestProperty",$model->properties[0]->name);
		$model->delete();
	}
}