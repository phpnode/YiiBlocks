<?php
Yii::import("packages.ypm.components.*");
Yii::import("packages.ypm.exceptions.*");
Yii::import("packages.ypm.models.*");
/**
 * Tests for the {@link APackageManager} class
 * @author Charles Pick
 * @package packages.ypm.tests
 */
class APackagManagerTest extends CTestCase {

	/**
	 * Tests the getPackages() method
	 */
	public function testGetPackages() {
		$manager = new APackageManager();
		$packages = $manager->getPackages();
		$this->assertTrue(isset($packages->ypm));
		$this->assertTrue($packages->ypm instanceof APackage);
	}
	/**
	 * Tests the getRepositories() method
	 */
	public function testGetRepositories() {
		$manger = new APackageManager();
		$repositories = $manger->getRepositories();
		$this->assertTrue(isset($repositories->local));
	}
}