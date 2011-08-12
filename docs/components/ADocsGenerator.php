<?php
/**
 * Generates documentation based on a list of files.
 * @author Charles Pick
 * @package packages.docs.components
 */
class ADocsGenerator extends CComponent {
	
	/**
	 * The file type handlers
	 * @var array
	 */
	public static $fileTypeHandlers = array(
		"php" => "APHPTokenizer",
		
	);
	
	/**
	 * The global namespace that is populated by parse()
	 * @var ANamespaceDoc
	 */
	public $namespace;
	
	/**
	 * The files to parse.
	 * @see CFileHelper::findFiles()
	 * @var array
	 */
	public $files = array();
	
	/**
	 * The files / folders to exclude from the documentation
	 * @var array
	 */
	public $exclude = array(".svn",".git",".cvs");
	/**
	 * The documentation set.
	 * @var ADocsSet
	 */
	public $set;
	/**
	 * Adds a file to the list
	 * @param string $filename The path to the file
	 * @return boolean whether the file was added or not
	 */
	public function addFile($filename) {
		if (!file_exists($filename)) {
			return false;
		}
		if (is_dir($filename)) {
			foreach(CFileHelper::findFiles(rtrim($filename,DIRECTORY_SEPARATOR),array(
				"exclude" => $this->exclude,
				"fileTypes" => array_keys(self::$fileTypeHandlers),
			)) as $filename) {
				$this->files[$filename] = $filename;
			}
		}
		else {
			$this->files[$filename] = $filename;
		}
		return true;
	}
	
	/**
	 * Removes a file from the list
	 * @param string $filename The path to the file
	 * @return boolean whether the file was removed or not
	 */
	public function removeFile($filename) {
		
		if (is_dir($filename)) {
			$removed = 0;
			foreach(CFileHelper::findFiles(rtrim($filename,DIRECTORY_SEPARATOR),array(
				"exclude" => $this->exclude,
				"fileTypes" => array_keys(self::$fileTypeHandlers),
			)) as $filename) {
				if (isset($this->files[$filename])) {
					unset($this->files[$filename]);
					$removed++;
				}
			}
			return $removed > 0;
		}
		else {
			if (isset($this->files[$filename])) {
				unset($this->files[$filename]);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Parses the files specified in $files
	 * @return ADocsGenerator $this with the parsed content
	 */
	public function parse() {
		$tokenizer = new APHPTokenizer;
		foreach($this->files as $file) {
			$this->namespace = $tokenizer->readFile($file);
		}
		$this->namespace->process();
		return $this;
	}
	/**
	 * Checks the files for documentation errors
	 * @return boolean true if the check passed, false if there were errors
	 */
	public function check() {
		return $this->namespace->check();
	}
}
