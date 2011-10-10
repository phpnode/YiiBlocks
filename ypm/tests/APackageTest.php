<?php
Yii::import("packages.ypm.components.*");
Yii::import("packages.ypm.exceptions.*");
Yii::import("packages.ypm.models.*");
/**
 * Tests for the {@link APackage} class
 * @author Charles Pick
 * @package packages.ypm.tests
 */
class APackageTest extends CTestCase {
	/**
	 * Tests for the save() method
	 */
	public function testSaveAndDelete() {
		$repo = APackageRepository::load("local");
		$package = new APackage();
		$this->assertFalse($repo->addPackage($package)); // name required
		$package->name = "ypm";
		$this->assertFalse($repo->addPackage($package)); // description required
		$package->description = "Yii Package Manager Test";
		$this->assertFalse($repo->addPackage($package)); // package already exists

		$package->name = "test123";
		$this->assertTrue($repo->addPackage($package)); // yay?

		$this->assertTrue($package->delete());
	}
	/**
	 * Tests for the load() method
	 */
	public function testLoad() {
		$this->assertFalse(APackage::load(uniqid()));
		$package = APackage::load("ypm");
		$this->assertTrue(is_object($package));
		$this->assertTrue($package instanceof APackage);
		$this->assertEquals("edit",$package->getScenario());
		$this->assertEquals("ypm",$package->name);
	}

	/**
	 * Tests the importDirectory() method
	 */
	public function testImportDirectory() {
		$package = new APackage();
		$package->name = "test123";
		$package->description = "Test package";
		$package->importDirectory(Yii::getPathOfAlias("packages.ypm.components"),"components");
		$this->assertTrue($package->save());
		$this->assertTrue(file_exists($package->getBaseDir()."/components/APackageManager.php"));
		$this->assertTrue($package->delete()); // clean up
		$this->assertFalse(file_exists($package->getBaseDir()."/components/APackageManager.php"));
	}

	/**
	 * Tests the importFile() method
	 */
	public function testImportFile() {
		$package = new APackage();
		$package->name = "test123";
		$package->description = "Test package";
		$package->importFile(Yii::getPathOfAlias("packages.ypm.components")."/APackageManager.php","components");
		$this->assertTrue($package->save());
		$this->assertTrue(file_exists($package->getBaseDir()."/components/APackageManager.php"));
		$this->assertTrue($package->delete()); // clean up
		$this->assertFalse(file_exists($package->getBaseDir()."/components/APackageManager.php"));
	}
}