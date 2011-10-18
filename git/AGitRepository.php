<?php
/**
 * Represents a git repository for interaction with git.
 *
 * @author Charles Pick
 * @package packages.git
 */
class AGitRepository extends CApplicationComponent {

	/**
	 * The path to the git executable.
	 * @var string
	 */
	public $gitPath = "git";

	/**
	 * The path to the git repository
	 * @var string
	 */
	protected $_path;

	/**
	 * The name of the current branch
	 * @var string
	 */
	protected $_currentBranch;

	/**
	 * Sets the path to the git repository folder.
	 * @param string $path the path to the repository folder
	 * @param boolean $createIfEmpty whether to create the repository folder if it doesn't exist
	 */
	public function setPath($path, $createIfEmpty = false)
	{
		if (!($realPath = realpath($path))) {
			if (!$createIfEmpty) {
				throw new InvalidArgumentException("The specified path does not exist");
			}
			mkdir($path);
			$realPath = realpath($path);
		}
		$this->_path = $realPath;
		if (!file_exists($realPath."/.git") || !is_dir($realPath."/.git")) {
			if (!$createIfEmpty) {
				throw new InvalidArgumentException("The specified path is not a git repository");
			}
			$this->run("init");
		}

	}

	/**
	 * Gets the path to the git repository folder
	 * @return string the path to the git repository folder
	 */
	public function getPath()
	{
		return $this->_path;
	}

	/**
	 * Runs a git command and returns the response
	 * @throws AGitException if git returns an error
	 * @param string $command the git command to run
	 * @return string the response from git
	 */
	public function run($command) {
		$descriptor = array(
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w'),
		);
		$pipes = array();
		$resource = proc_open($this->gitPath." ".$command,$descriptor,$pipes,$this->getPath());
		$stdout = stream_get_contents($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);
		foreach($pipes as $pipe) {
			fclose($pipe);
		}
		if (trim(proc_close($resource)) && $stderr) {
			throw new AGitException($stderr);
		}
		return trim($stdout);
	}

	/**
	 * Adds a file or array of files to the git repository
	 * @throws AGitException if there was an error adding the file
	 * @param string|array $file the file or files to add, pass an array to add multiple files
	 */
	public function add($file) {
		if (is_array($file)) {
			foreach(array_values($file) as $file) {
				$this->add($file);
			}
			return;
		}
		if (!file_exists($file) && !(substr($file,0,1) != "/" && file_exists($this->getPath()."/".$file) )) {
			throw new AGitException("Cannot add ".$file." to the repository because it doesn't exist");
		}
		$this->run("add ".$file);
	}
	/**
	 * Removes a file or array of files from the git repository
	 * @throws AGitException if there was an error removing the file
	 * @param string|array $file the file or files to remove, pass an array to remove multiple files
	 * @param boolean $force whether to force removal of the file, even if there are staged changes
	 */
	public function rm($file, $force = false) {
		if (is_array($file)) {
			foreach(array_values($file) as $file) {
				$this->rm($file);
			}
			return;
		}
		if (!file_exists($file) && !(substr($file,0,1) != "/" && file_exists($this->getPath()."/".$file) )) {
			throw new AGitException("Cannot remove ".$file." from the repository because it doesn't exist");
		}
		if ($force) {
			$this->run("rm -f ".$file);
		}
		else {
			$this->run("rm ".$file);
		}
	}

	/**
	 * Makes a git commit
	 * @param string $message the commit message
	 * @param boolean $addFiles whether to add changes from all known files
	 * @param boolean $amend whether the commit
	 * @return array|false an array of committed files, file => status, or false if the commit failed
	 */
	public function commit($message = null, $addFiles = false, $amend = false) {
		$command = "commit ";
		if ($addFiles) {
			$command .= "-a ";
		}
		if ($amend) {
			$command .= "--amend";
		}
		if ($message) {
			$command .= '-m "'.$message.'"';
		}

		$result = $this->run($command." --porcelain");
		if (!$result) {
			return false;
		}
		$this->run($command);
		$files = array();
		foreach(explode("\n",$result) as $line) {
			$status = trim(substr($line,0,3));
			$file = trim(substr($line,3));
			$files[$file] = $status;
		}
		return $files;
	}

	/**
	 * Gets an array of paths that have differences
	 * between the index file and the current HEAD commit
	 * @return array the differences, filename => status
	 */
	public function status() {
		$files = array();
		foreach(explode("\n", $this->run("status --porcelain")) as $n => $line) {
			$status = trim(substr($line,0,3));
			$file = trim(substr($line,3));
			$files[$file] = $status;
		}
		return $files;
	}
	/**
	 * Switches to the given branch
	 * @param string $branchName the name of the branch to check out
	 * @param boolean $create whether to create the branch or not
	 * @param boolean $force whether to force the checkout or not
	 * @return string the response of the checkout
	 */
	public function checkout($branchName, $create = false, $force = false) {
		$command = "checkout ";
		if ($create) {
			$command .= "-b ";
		}
		if ($force) {
			$command .= "-f ";
		}
		$command .= $branchName;
		return $this->run($command);
	}

	/**
	 * Gets the name of the current branch
	 * @return string the name of the current branch
	 */
	public function getCurrentBranch() {
		$this->getBranches();
		return $this->_currentBranch;
	}

	/**
	 * Gets a list of git branches
	 * @return array an array of branch names
	 */
	public function getBranches() {
		$branches = array();
		foreach(explode("\n",$this->run("branch")) as $branch) {
			if (substr($branch,0,2) == "* ") {
				$this->_currentBranch = substr($branch,2);
			}
			$branches[] = substr($branch,2);
		}
		return $branches;
	}


}