<?php
/**
 * Adds some common functionality to CFileHelper
 * @author Charles Pick
 * @package packages.fileBrowser
 */
class AFileHelper extends CFileHelper {
	
	/**
	 * Returns the directories found under the specified directory and subdirectories.
	 * @param string $dir the directory under which the files will be looked for
	 * @param array $options options for directory searching. Valid options are:
	 * <ul>
	 * <li>exclude: array, list of directory exclusions. Each exclusion can be either a name or a path.
	 * If a directory name or path matches the exclusion, it will not be copied. For example, an exclusion of
	 * '.svn' will exclude all directories whose name is '.svn'. And an exclusion of '/a/b' will exclude
	 * directory '$src/a/b'. Note, that '/' should be used as separator regardless of the value of the DIRECTORY_SEPARATOR constant.
	 * </li>
	 * <li>level: integer, recursion depth, default=-1.
	 * Level -1 means searching for all directories under the directory;
	 * Level 0 means searching for only the directories DIRECTLY under the directory;
	 * level N means searching for those directories that are within N levels.
 	 * </li>
	 * </ul>
	 * @return array directories found under the directory. The directory list is sorted.
	 */
	public static function findDirectories($dir,$options=array())
	{
		$exclude=array();
		$level=-1;
		extract($options);
		$list=self::findDirectoriesRecursive($dir,'',$exclude,$level);
		sort($list);
		return $list;
	}

	/**
	 * Returns the directories found under the specified directory and subdirectories.
	 * This method is mainly used by {@link findDirectories}.
	 * @param string $dir the source directory
	 * @param string $base the path relative to the original source directory
	 * @param array $exclude list of directory exclusions. Each exclusion can be either a name or a path.
	 * If a directory name or path matches the exclusion, it will not be copied. For example, an exclusion of
	 * '.svn' will exclude all directories whose name is '.svn'. And an exclusion of '/a/b' will exclude
	 * directory '$src/a/b'. Note, that '/' should be used as separator regardless of the value of the DIRECTORY_SEPARATOR constant.
	 * @param integer $level recursion depth. It defaults to -1.
	 * Level -1 means searching for all directories and files under the directory;
	 * Level 0 means searching for only the files DIRECTLY under the directory;
	 * level N means searching for those directories that are within N levels.
	 * @return array directories found under the directory.
	 */
	protected static function findDirectoriesRecursive($dir,$base,$exclude,$level)
	{
		$list=array();
		$handle=opendir($dir);
		while(($file=readdir($handle))!==false)
		{
			if($file==='.' || $file==='..')
				continue;
			$path=$dir.DIRECTORY_SEPARATOR.$file;
			$isDir=is_dir($path);
			if($isDir && self::validatePath($base,$file,false,array(),$exclude))
			{
				$list[]=$path;
				if($level)
					$list=array_merge($list,self::findDirectoriesRecursive($path,$base.'/'.$file,$exclude,$level-1));
			}
		}
		closedir($handle);
		return $list;
	}
}
