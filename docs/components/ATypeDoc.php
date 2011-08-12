<?php
/**
 * Represents information about a data type. All other doc types extend this class.
 * {@link AFunctionDoc::cleanType() A TEST} a test link
 * @author Charles Pick
 * @package packages.docs
 */
class ATypeDoc extends CComponent {
	/**
	 * The name of the type
	 * @var string
	 */
	public $name;
	
	/**
	 * The namespace for this type
	 * @var string
	 */
	public $namespace;
	
	/**
	 * The doc comment for this type
	 * @var string
	 */
	public $comment;
	
	/**
	 * The introduction for this type
	 * @var string
	 */
	protected $_introduction;
	/**
	 * The description for this type
	 * @var string
	 */
	protected $_description;
	
	/**
	 * The doc tags for this type
	 * @var APHPDocTag[]
	 */
	public $tags = array();
		
	/**
	 * The path to the the file for the type declaration
	 * @var string
	 */
	public $filename;
	
	/**
	 * The name of package this type belongs to
	 * @var string
	 */
	public $package;
	
	/**
	 * The start line for the type declaration
	 * @var integer
	 */
	public $startLine;
	
	/**
	 * The end line for the type declaration
	 * @var integer
	 */
	public $endLine;
	
	/**
	 * The programming language for this type.
	 * E.g php
	 * @var string 
	 */
	public $language;
	
	/**
	 * Whether this class is processed or not
	 * @var boolean
	 */
	public $isProcessed = false;
	
	
	
	/**
	 * Constructor, sets the item attributes
	 * @param array $config The configuration attribute => array
	 */
	public function __construct($config = null) {
		if ($config !== null) {
			foreach($config as $attribute => $value) {
				if (isset($this->{$attribute})) {
					$this->{$attribute} = $value;
				}
			}
		}
	}
	
	/**
	 * Processes the type, parses the doc comment etc.
	 * This should be called after the current parse has finished.
	 * Child classes that override this should call the parent implementation.
	 * @return boolean Whether this was processed or not
	 * 
	 */
	public function process() {
		if ($this->isProcessed) {
			return false; // already processed
		}
		$this->parseDocComment();
		$this->isProcessed = true;
		return true;
	}
	
	/**
	 * Parses the doc comment
	 */
	protected function parseDocComment() {
		if ($this->comment != "") {
			$parser = new APHPDocParser;
			$parser->parse($this->comment);
			$this->_introduction = $parser->introduction;
			$this->_description = $parser->description;
			$this->tags = $parser->tags;
			foreach($this->tags as $tag) {
				if ($tag->tagName == "package" && $this->package == "") {
					$this->package = $tag->value;
				}
			}
		}
	}
	/**
	 * Gets the description for this item
	 * @return string the description for this item
	 */
	public function getDescription() {
		return preg_replace_callback('/\{@link\s+([^\s\}]+)(.*?)\}/s',array($this,'fixLink'),$this->_description);
	}
	
	/**
	 * Gets the introduction for this item
	 * @return string the introduction for this item
	 */
	public function getIntroduction() {
		return preg_replace_callback('/\{@link\s+([^\s\}]+)(.*?)\}/s',array($this,'fixLink'),$this->_introduction);
	}
	/**
	 * Fixes links in introduction and descriptions
	 * @param array $matches the matched link to fix
	 * @return string the fixed link, if possible
	 */
	public function fixLink($matches) {
		$url=$matches[1];
		if(($text=trim($matches[2]))==='') {
			$text=$url;
		}
		if(preg_match('/^(http|ftp):\/\//i',$url)) {  
			// an external URL
			return "<a href=\"$url\">$text</a>";
		}
		return ($url === '' ? $text : $this->typeLink($url,$text));
		
	}
	
	
	/**
	 * Generates a documentation link for an item of the specified type.
	 * If no such type can be found the type text will be returned instead 
	 * @param string $rawType the type, e.g. CComponent
	 * @param string $label the label for the link, if null the rawType will be used
	 * @return string the link to this type
	 */
	public function typeLink($rawType, $label = null) {
		if ($label === null) {
			$label = $rawType;
		}
		if (!is_object($this->namespace)) {
			return $label;
		}
		return $this->namespace->typeLink($rawType, $label);
		
	}
	/**
	 * Gets the signature for this type
	 * @return string the signature to display for this type
	 */
	public function signature() {
		return $this->name;
	}
	/**
	 * Logs a documentation error
	 * @param string $level the error level e.g. "info", "warning" or "error"
	 * @param string $message the error message
	 * @param integer $startLine the start line that triggered this error
	 * @param integer $endLine the end line that triggered this error
	 */
	public function log($level, $message,$startLine = null, $endLine = null) {
		if ($startLine === null) {
			$startLine = $this->startLine;
		}
		if ($endLine === null) {
			$endLine = $this->endLine;
		}
		echo "[$level] $message";
		if ($this->filename != "") {
			echo " (in file: $this->filename [$startLine:$endLine])\n";
		}
		else {
			echo "\n";
		}
		
	}
	/**
	 * Checks the documentation for this type
	 * @return boolean true if no errors were logged, otherwise false;
	 */
	public function check() {
		$hasErrors = false;
		if ($this->name != "") {
			if ($this->description == "") {
				$this->log("warning","No description for ".$this->name,$this->startLine,$this->endLine);
				$hasErrors = true;
			}
		}
		return !$hasErrors;
	}
	/**
	 * Gets the value of the since tag for this item
	 * @since 0.01
	 * @return string the value of the since tag, or null if there's no such tag
	 */
	public function getSince() {
		foreach($this->tags as $tag) {
			if (strtolower($tag->tagName) == "since") {
				return $tag->value;
			}
		}
	}
	
	/**
	 * Gets the value of the see tags for this item
	 * @return array the value of the see tags
	 */
	public function getSee() {
		$see = array();
		foreach($this->tags as $tag) {
			if (strtolower($tag->tagName) == "see") {
				$see[] = $tag->value;
			}
		}
		return $see;
	}
	/**
	 * Cleans a type link
	 * @param string $type The type, e.g. Model::someFunc()
	 * @return array 2 values, the type and the fragment if any, e.g. array("Model", "someFunc")
	 */
	protected function cleanType($type) {
		$fragment = null;
		if (strstr($type,"[")) {
			$type = array_shift(explode("[",$type));
		}
		if (strstr($type,"::")) {
			$type = explode("::",$type);
			$fragment = array_pop($type);
			$type = array_shift($type);
		}
		elseif (strstr($type,"->")) {
			$type = explode("->",$type);
			$fragment = array_pop($type);
			$type = array_shift($type);
		}
		elseif (strstr($type,".")) {
			$type = explode(".",$type);
			$fragment = array_pop($type);
			$type = array_shift($type);
		}
		if ($fragment !== null && substr($fragment,-2,2) == "()") {
			$fragment = substr($fragment,0,-2);
		}
		if (substr($type,-2,2) == "()") {
			$type = substr($type,0,-2);
		}
		
		return array($type,$fragment);
	}
	/**
	 * Gets the source code for this item
	 * @return string the php source code for this item
	 */
	public function getSourceCode() {
		$contents = file($this->filename);
		return implode("",array_slice($contents,$this->startLine - 1,($this->endLine - $this->startLine) + 1));
	}
	
	
}
