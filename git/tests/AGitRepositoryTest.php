<?php
Yii::import("packages.git.*");
class AGitRepositoryTest extends CTestCase {
	/**
	 * The path to the test repository
	 * @var string
	 */
	public $path = "/tmp/gitrepositorytest/";

	/**
	 * The git repository instance to use for testing
	 * @var AGitRepository
	 */
	protected $_repository;

	/**
	 * Holds a list of example files to add to the repository
	 * @var array
	 */
	protected $_files;
	/**
	 * Tests basic functionality
	 */
	public function testBasics() {
		$repo = $this->getRepository();
		$repo->add($this->getFiles()); // add all the files in one go

		$changedFiles = $repo->status();

		foreach($this->getFiles() as $file) {
			$this->assertTrue(isset($changedFiles[$file]));
		}

		$commitMessage = "Test Commit: ".uniqid();
		$response = $repo->commit($commitMessage); // commit our changes

		$this->assertTrue(is_array($response));
		foreach($this->getFiles() as $file) {
			$this->assertTrue(isset($response[$file])); // check our files were committed
		}

		$repo->rm($this->getFiles()); // delete our files
		foreach($this->getFiles() as $file) {
			$this->assertFalse(file_exists($this->path."/".$file)); // check our removal was successful
		}

		$commitMessage = "Test Commit: ".uniqid();
		$response = $repo->commit($commitMessage); // commit our deletions

		$this->assertTrue(is_array($response));

	}



	/**
	 * Tests the commit() method
	 */
	public function testCommit() {
		$repo = $this->getRepository();
		$this->assertFalse($repo->commit("test")); // no changes, should fail
		$files = $this->getFiles();
		foreach($files as $file) {
			$repo->add($this->path."/".$file);
			file_put_contents($this->path."/".$file,uniqid());
		}
		$commitMessage = "Test Commit: ".uniqid();
		$response = $repo->commit($commitMessage,true);

		$this->assertTrue(is_array($response));
		foreach($this->getFiles() as $file) {
			$this->assertTrue(isset($response[$file]));
		}
	}

	public function testCheckout() {
		$repo = $this->getRepository();
		$this->assertEquals("master",$repo->getCurrentBranch());
		$branchName = "test-branch-".uniqid();
		$repo->checkout($branchName,true);
		$repo->getCurrentBranch();
		$this->assertEquals($branchName,$repo->getCurrentBranch());
		$repo->checkout("master");
		$this->assertEquals("master",$repo->getCurrentBranch());
	}

	/**
	 * Tests running git commands
	 */
	public function testRun() {
		$path = $this->path;
		$repo = $this->getRepository();


		#print_r();
		print_r($repo->run("commit --porcelain"));



	}

	/**
	 * Gets the repository to use for testing
	 * @return AGitRepository the respository for testing
	 */
	protected function getRepository() {
		if ($this->_repository === null) {
			$this->_repository = new AGitRepository();
			$this->_repository->setPath($this->path,true);
			$this->assertTrue(file_exists($this->path));
		}
		return $this->_repository;
	}

	/**
	 * Gets an array of filenames that should be added to git
	 * @return array
	 */
	protected function getFiles() {
		if ($this->_files === null) {
			$files = array(
				"test.txt" => uniqid(),
				"test2.txt" => uniqid(),
				"test3.txt" => uniqid(),
			);
			foreach($files as $file => $content) {
				file_put_contents($this->path."/".$file,$content);
				$this->assertTrue(file_exists($this->path."/".$file));
			}
			$this->_files = array_keys($files);
		}
		return $this->_files;
	}
}