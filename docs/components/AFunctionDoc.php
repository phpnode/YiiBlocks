<?php
/**
 * Represents documentation information about a function.
 * @author Charles Pick
 * @package packages.docs
 */
class AFunctionDoc extends ATypeDoc {
	
	/**
	 * The author of this function
	 * @var string
	 */
	public $author;
	
	/**
	 * The version of this function
	 * @var string
	 */
	public $version;

	/**
	 * A list of function parameters.
	 * @var CAttributeCollection
	 */
	public $parameters;
	
	/**
	 * The type of whatever is returned by this function
	 * @var AReturnDoc
	 */
	public $return;
	
	/**
	 * Whether this function contains a return statement.
	 * @var boolean
	 */
	public $returns = false;
	
	/**
	 * Constructor, sets the item attributes
	 * @see ATypeDoc::__construct()
	 * @param array $config The configuration attribute => array
	 */
	public function __construct($config = null) {
		parent::__construct($config);
		$properties = array("parameters");
		foreach($properties as $property) {
			if (!$this->{$property} instanceof CAttributeCollection) {
				$this->{$property} = new CAttributeCollection($this->{$property});
			}
		}
	}
	
	/**
	 * Processes the function.
	 * @see ATypeDoc::process()
	 * @return boolean Whether this was processed or not
	 */
	public function process() {
		if (parent::process()) {
			foreach($this->parameters as $parameter) {
				$parameter->process();
			}
			return true;
		}
		else {
			return false;
		}
		
	}
	
	/**
	 * Gets the signature for this function
	 * @return string the signature to display for this function
	 */
	public function signature() {
		$signature = "";
		
		$signature .= "function ".$this->name." ";
		$signature .= "(";
		$params = array();
		foreach($this->parameters as $parameter) {
			$params[] = $parameter->signature();
		}
		$signature .= implode(", ",$params);
		$signature .= ")";
		return $signature;
	}
	/**
	 * Parses the doc comment
	 */
	protected function parseDocComment() {
		if ($this->comment != "") {
			parent::parseDocComment();
			foreach($this->tags as $tag) {
				if ($tag->tagName == "author") {
					if (is_array($this->author)) {
						$this->author[] = $tag->value;
					}
					else if ($this->author != "") {
						$this->author = array($this->author);
						$this->author[] = $tag->value;
					}
					else {
						$this->author = $tag->value;
					}
				}
				elseif ($tag->tagName == "version" && $this->version == "") {
					$this->version = $tag->value;
				}
				elseif ($tag->tagName == "param" && isset($this->parameters[$tag->name])) {
					$this->parameters[$tag->name]->type = $tag->type;
					$this->parameters[$tag->name]->comment = $tag->comment;
				}
				elseif($tag->tagName == "return" && $this->return === null) {
					$this->return = new AReturnDoc;
					$this->return->filename = $this->filename;
					$this->return->startLine = $this->startLine;
					$this->return->endLine = $this->endLine;
					$this->return->type = $tag->type;
					$this->return->comment = $tag->comment;
					$this->return->function = $this;
					$this->return->process();
				}
			}
		}
	}
	/**
	 * Gets the value of the author tag(s) for this item
	 * @return array the value of the author tags
	 */
	public function getAuthors() {
		$authors = array();
		foreach($this->tags as $tag) {
			if (strtolower($tag->tagName) == "author") {
				$authors[] = $tag->value;
			}
		}
		return $authors;
	}
	/**
	 * Checks the documentation for this type
	 * @return boolean true if no errors were logged, otherwise false;
	 */
	public function check() {
		$hasErrors = !parent::check();
		if ($this->returns) {
			if (!is_object($this->return)) {
				$this->log("info","Undocumented return statement");
			}
			else {
				$this->return->check();
			}
		}
		foreach($this->parameters as $param) {
			if (!$param->check()) {
				$hasErrors = true;
			}
		}
		return !$hasErrors;
	}
	
	/**
	 * Gets the documentation filename for this function
	 * @return string the doc file name
	 */
	public function getDocFilename() {
		return $this->name."-function.html";
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
		return $this->namespace->typeLink($rawType,$label);
		
	}
}
