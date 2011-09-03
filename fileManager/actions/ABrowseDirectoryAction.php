<?php
/**
 * Provides the JSON for AFileBrowser, allows the user to browse the contents of
 * certain directories as specified by basePath. 
 * @author Charles Pick
 * @package packages.fileManager.actions
 */
class ABrowseDirectoryAction extends CAction {
	/**
	 * The base path(s) to allow the user to browse.
	 * Warning: Any files not explicitly excluded by $this->exclude 
	 * will be visible to the user!
	 * This can be either a string or an array of strings
	 * if multiple base paths are required.
	 * @var mixed
	 */
	public $basePath;
	
	/**
	 * An array of file types that should be included
	 * in the results.
	 * Defaults to an empty array meaning all files will be included.
	 * @var array
	 */
	public $fileTypes = array();
	
	/**
	 * An array of files / directories that will be excluded from
	 * the results presented to the user
	 * @var array
	 */
	public $exclude = array();
	
	/**
	 * Renders the JSON for the files
	 * @param string $path The path of the directory to browse, this must be under the basePath
	 */
	public function run($path) {
		$realPath = realpath($path);
		if (!$realPath) {
			throw new CHttpException(404,Yii::t("packages.fileManager","No such path"));
		}
		$matched = false;
		if (is_array($this->basePath)) {
			foreach($this->basePath as $basePath) {
				$basePath = realpath($basePath);
				if (substr($realPath,0,strlen($basePath)) == $basePath) {
					$matched = true;
					break;
				}
			}
		}
		else {
			$basePath = realpath($basePath);
			if (substr($realPath,0,strlen($basePath)) == $basePath) {
				$matched = true;
			}
		}
		if (!$matched) {
			throw new CHttpException(404,Yii::t("packages.fileManager","No such path"));
		}
		// path is allowed so lets load some files
		
		$files = array();
		foreach(CFileHelper::findFiles($realPath,array("level" => 0, "exclude" => $this->exclude, "fileTypes" => $this->fileTypes)) as $file) {
			$item = array(
				"name" => basename($file),
				"path" => $file,
				"size" => filesize($file),
			);
			
			$files[] = $item;
		}
		// and some directories
		$directories = array();
		foreach(AFileHelper::findDirectories($realPath,array("level" => 0, "exclude" => $this->exclude)) as $dir) {
			$item = array(
				"name" => basename($dir),
				"path" => $dir,
			);
			
			$directories[] = $item;
		}
		$response = new AJSONResponse;
		$response->directories = $directories;
		$response->files = $files;
		$response->render();
	}
}
