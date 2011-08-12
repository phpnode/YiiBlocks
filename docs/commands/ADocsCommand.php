<?php
Yii::import("packages.docs.renderers.*");
/**
 * Provides access to the documentation generator via the command line
 * @property string $fakeProperty fake property description
 * 
 * @author Charles Pick
 * @package packages.docs.commands
 */
class ADocsCommand extends CConsoleCommand {
	/**
	 * Holds the documentation generator
	 * @var ADocsGenerator
	 */
	protected $_generator;
	
	/**
	 * Holds the current documentation set name
	 * @see getSetName()
	 * @see setSetName()
	 * @var ADocsSet
	 */
	protected $_setName;
	
	/**
	 * Gets the docs generator
	 * @return ADocsGenerator the documentation generator
	 */
	public function getGenerator() {
		if ($this->_generator === null) {
			$this->_generator = ADocsSet::load($this->setName);
		}
		return $this->_generator;
	}
	/**
	 * Sets the docs generator
	 * @param ADocsGenerator $generator The documentation generator
	 * @return ADocsGenerator the documentation generator
	 */
	public function setGenerator(ADocsGenerator $generator) {
		return $this->_generator = $generator;
	}
	/**
	 * Gets the documentation set name
	 * @return string The documentation set name
	 */
	public function getSetName() {
		if ($this->_setName === null) {
			$this->_setName = Yii::app()->getGlobalState(__CLASS__.".setName","default");
		}
		return $this->_setName;
	}
	/**
	 * Sets the documentation set name
	 * @param string $setName the documentation set name
	 * @return string the set name
	 */
	public function setSetName($setName) {
		Yii::app()->setGlobalState(__CLASS__.".setName",$setName);
		return $this->_setName = $setName;
	}
	/**
	 * The default action, shows the status
	 * @param string $params The parameters for the command
	 */
	public function run($params) {
		if (!isset($params[0])) {
			// no parameters, show the status
			return;
		}
		switch(strtolower($params[0])) {
			case "status":
				$this->status();
				break;
			case "use":
				if (!isset($params[1])) {
					echo "Documentation set name: $this->setName\n";
					return;
				}
				if ($this->useSet($params[1])) {
					echo "Switched to documentation set: $this->setName\n";
				}
				else {
					echo "ERROR: No such documentation set: ".$params[1]."\n";
				}
				break;
			case "addset":
				if (!isset($params[1])) {
					echo "ERROR: No set name supplied!";
					return;
				}
				
				if ($this->addSet(trim($params[1]))) {
					echo "Added documentation set: $this->setName\n";
					echo "Switched to documentation set: $this->setName\n";
				}
				else {
					echo "ERROR: Could not create documentation set: ".$params[1].". A set with this name already exists.\n";
				}
				break;
			case "add":
				if (!isset($params[1])) {
					echo "ERROR: No file or directory name to add!";
					return;
				}
				$fileCount = count($this->generator->files);
				if ($this->addFile(trim($params[1]))) {
					$newFileCount = count($this->generator->files);
					echo "Added ".($newFileCount - $fileCount)," file(s) to documentation set: $this->setName.\n";
				}
				else {
					echo "ERROR: Could not add file: ".$params[1].". Please ensure it exists.\n";
				}
				break;
			case "remove":
				if (!isset($params[1])) {
					echo "ERROR: No file or directory name to remove!";
					return;
				}
				$fileCount = count($this->generator->files);
				if ($this->removeFile(trim($params[1]))) {
					$newFileCount = count($this->generator->files);
					echo "Removed ".($fileCount - $newFileCount)," file(s) from documentation set: $this->setName.\n";
				}
				else {
					echo "ERROR: Could not remove file: ".$params[1].". It is not currently being tracked..\n";
				}
				break;
			case "cleanup":
				$this->cleanup();
				break;
			case "check":
				echo "Checking code for documentation errors....\n";
				
				$this->check(isset($params[1]) ? $params[1] : null);
				echo "\nDone.\n";
				break; 
			case "generate":
				$this->generate($params[1]);
				break;
		}
	}
	/**
	 * Show the documentation status
	 */
	public function status() {
		echo "Documentation set name: $this->setName\n";
		$fileCount = count($this->generator->files);
		echo "Tracking $fileCount file(s)\n";
		$missingFiles = array();
		foreach($this->generator->files as $file) {
			if (file_exists($file)) {
				echo "\t$file\n";
			}
			else {
				echo "\t$file\tWARNING - FILE DOES NOT EXIST!\n";
				$missingFiles[] = $file;
			}
		}
		if (count($missingFiles)) {
			echo "\nThere are ".count($missingFiles)." files that are being tracked but have been removed.\n";
			echo "Run ./yiic docs cleanup to remove them, or remove them individually with ./yiic docs remove <filename>\n";
			echo "Missing File(s):\n";
			foreach($missingFiles as $file) {
				echo "\t$file\n";
			}
		}
	}
	/**
	 * Switch to a particular set
	 * @param string $name The name of the set to use
	 * @return boolean Whether the switch was successful or not
	 */
	public function useSet($name) {
		
		$generator = ADocsSet::load($name);
		if (is_object($generator)) {
			$this->generator = $generator;
			$this->setName = $name;
			return true;
		}
		else {
			return false;
		}
	}
	/**
	 * Adds a set with the given name
	 * @param string $name The name of the set
	 * @return boolean true if the set was added, otherwise false
	 */
	public function addSet($name) {
		if (is_object(ADocsSet::load($name))) {
			return false; // already exists
		}
		$generator =  new ADocsGenerator;
		$set = new ADocsSet(null,$generator);
		$set->name = $name;
		if ($set->save()) {
			$this->setName = $name;
			$this->generator = $generator;
			return true;
		}
		else {
			return false;
		}
	}
	/**
	 * Adds a given file or directory to the set
	 * @param string $filename the file to add
	 * @return boolean true if the file was added, otherwise false
	 */
	public function addFile($filename) {
		if ($this->generator->addFile($filename)) {
			return $this->generator->set->save();
		}
		else {
			return false;
		}
	}
	
	/**
	 * Removes a given file or directory from the set
	 * @param string $filename the file to remove
	 * @return boolean true if the file was removed, otherwise false
	 */
	public function removeFile($filename) {
		if ($this->generator->removeFile($filename)) {
			return $this->generator->set->save();
		}
		else {
			return false;
		}
	}
	/**
	 * Cleans up missing files
	 */
	public function cleanUp() {
		$missingFiles = array();
		foreach($this->generator->files as $n => $file) {
			if (!file_exists($file)) {
				$missingFiles[] = $file;
				unset($this->generator->files[$n]);
			}
		}
		if (count($missingFiles)) {
			$this->generator->set->save();
			echo "Removed ".count($missingFiles)." from documentation set: $this->setName.\n";
			foreach($missingFiles as $file) {
				echo "\t$file\n";
			}
		}
	}
	/**
	 * Checks the code for documentation errors
	 * @param string $filename The file to check, if not specified the whole set will be checked
	 * @return boolean whether the check succeeded or not
	 */
	public function check($filename = null) {
		if ($filename === null) {
			return $this->generator->parse()->check();
		}
		$generator = new ADocsGenerator;
		$generator->files[] = $filename;
		return $generator->parse()->check();
	}
	/**
	 * Generates the documentation
	 * @param string $outputDir the output directory
	 */
	public function generate($outputDir) {
		$outputDir = rtrim($outputDir,"/");
		$renderer = new ADocsRenderer;
		echo "Parsing source files...";
		$startTime = microtime(true);
		$renderer->generator = $this->generator->parse();
		$endTime = microtime(true);
		echo "done in ".($endTime - $startTime)." seconds\n";
		echo "\nPeak memory usage: ".memory_get_peak_usage()."\n";
		$startTime = $endTime;
		echo "Rendering documentation...";
		$renderer->outputDir = $outputDir;
		$renderer->render();
		$endTime = microtime(true);
		echo "done in ".($endTime - $startTime)." seconds\n";
		
		echo "\nPeak memory usage: ".memory_get_peak_usage()."\n";
	}
}
