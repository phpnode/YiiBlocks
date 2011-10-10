<?php
Yii::import("packages.ypm.components.*");
Yii::import("packages.ypm.exceptions.*");
Yii::import("packages.ypm.models.*");
/**
 * Tests for the {@link APackageRepository} class
 * @author Charles Pick
 * @package packages.ypm.tests
 */
class APackageRepositoryTest extends CTestCase {
	/**
	 * Tests for the save() method
	 */
	public function testSaveAndDelete() {
		$repo = new APackageRepository();
		$this->assertFalse($repo->save()); // name required
		$repo->name = "local";
		$this->assertFalse($repo->save()); // description required
		$repo->description = "Yii Package Repository Test";

		$this->assertFalse($repo->save()); // repo already exists

		$repo->name = "localRepoTest";
		$this->assertTrue($repo->save()); // yay?
		$this->assertFalse($repo->getIsRemote());
		$this->assertTrue($repo->delete());
	}
	/**
	 * Tests for the load() method
	 */
	public function testLoad() {
		$this->assertFalse(APackageRepository::load(uniqid()));
		$repo = Yii::app()->packageManager->getRepositories()->local;
		$this->assertTrue(is_object($repo));
		$this->assertTrue($repo instanceof APackageRepository);
		$this->assertEquals("edit",$repo->getScenario());
		$this->assertEquals("local",$repo->name);
	}

	public function testPackages() {
		$repo = Yii::app()->packageManager->getRepositories()->local;

		$this->assertTrue($repo instanceof APackageRepository);

		$package = new APackage();
		$package->name = "testPackage";
		$package->description = "test test test";
		$this->assertTrue($repo->addPackage($package));
		$this->assertTrue(isset($repo->getPackages()->testPackage));
		$this->assertTrue($package->delete());
		$this->assertFalse(isset($repo->getPackages()->testPackage));
	}
}