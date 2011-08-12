<?php
/**
 * A parser for PHPDoc comments
 * @author Charles Pick
 * @package packages.docs.components
 */
class APHPDocParser extends CComponent {
	/**
	 * The introduction for this comment
	 * @var string
	 */
	public $introduction;
	
	/**
	 * The description, extracted from this comment
	 * @var string
	 */
	public $description;
	
	/**
	 * The PHPDoc tags found in the comment content.
	 */
	public $tags = array();
	
	/**
	 * Parses a raw PHP doc comment
	 * @param string $rawComment The raw PHPDoc comment to parse
	 * @return APHPDocParser The parser with the parsed content
	 */
	public function parse($rawComment) {
		
		$comment=strtr(trim(preg_replace('/^\s*\**( |\t)?/m','',trim(trim($rawComment),'/'))),"\r",'');
		if(preg_match('/^\s*@\w+/m',$comment,$matches,PREG_OFFSET_CAPTURE)) {
			$meta=substr($comment,$matches[0][1]);
			$comment=trim(substr($comment,0,$matches[0][1]));
		}
		else {
			$meta='';
		}
		if(($pos=strpos($comment,"\n"))!==false) {
			$this->introduction = $this->processDescription(substr($comment,0,$pos));
		}
		else {
			$this->introduction = $this->processDescription($comment);
		}

		$this->description=$this->processDescription($comment);
		$this->tags = $this->processTags($meta);
		return $this;
	}
	/**
	 * Processes the tags in a comment
	 * @param string $comment The comment to process
	 * @return array the processed tags
	 */
	protected function processTags($comment) {
		$tags=preg_split('/^\s*@/m',$comment,-1,PREG_SPLIT_NO_EMPTY);
		foreach($tags as $tag) {
			$segs=preg_split('/\s+/',trim($tag),2);
			$tagName=$segs[0];
			$param=isset($segs[1])?trim($segs[1]):'';
			$tagClass = 'APHPDoc'.ucfirst($tagName)."Tag";
			if (class_exists($tagClass,false)) {
				$tag = new $tagClass($tagName,$param);
			}
			else {
				$tag = new APHPDocTag($tagName,$param);
			}
			$this->tags[] = $tag;
		}
		return $this->tags;
	}
	/**
	 * Processes the given description
	 * @param string $text the description to process
	 * @return string The processed description
	 */
	protected function processDescription($text) {
		if(($text=trim($text))==='')
			return '';
		$text=preg_replace('/^(\r| |\t)*$/m',"<br/><br/>",$text);
		$text=preg_replace_callback('/<pre>(.*?)<\/pre>/is',array($this,'processCode'),$text);
		#$text=preg_replace_callback('/\{@link\s+([^\s\}]+)(.*?)\}/s',array($this,'processLink'),$text);
		return $text;
	}
	
	protected function processCode($matches)
	{
		$match=preg_replace('/<br\/><br\/>/','',$matches[1]);
		return "<pre>".htmlspecialchars($match)."</pre>";
	}
	/**
	 * @todo add a comment here
	 */
	protected function processLink($matches)
	{
		$url=$matches[1];
		if(($text=trim($matches[2]))==='') {
			$text=$url;
		}
		if(preg_match('/^(http|ftp):\/\//i',$url)) {  
			// an external URL
			return "<a href=\"$url\">$text</a>";
		}
		
		return $url===''?$text:'{{'.$url.'|'.$text.'}}';
	}
	/**
	 * Returns the description, if this has been processed
	 * @return string the comment text
	 */
	public function __toString() {
		return $this->description;
	}
}
/**
 * Holds information about a phpdoc tag
 * @author Charles Pick
 * @package packages.docs.components
 */
class APHPDocTag extends CComponent {
	/**
	 * The name of the tag
	 * @var string
	 */
	public $tagName;
	
	/**
	 * The tag value
	 * @var string
	 */
	public $value;
	/**
	 * The tag comment, if any
	 * @var string
	 */
	public $comment;
	/**
	 * The constructor
	 * @param string $name The name of the tag
	 * @param string $param The parameter for the tag
	 */
	public function __construct($name, $param = null) {
		$this->tagName = $name;
		$this->value = $param;
	}
	
}

/**
 * Holds information about a phpdoc see tag
 * @author Charles Pick
 * @package packages.docs.components
 */
class APHPDocSeeTag extends APHPDocTag {
	
}

/**
 * Holds information about a phpdoc var tag
 * @author Charles Pick
 * @package packages.docs.components
 */
class APHPDocVarTag extends APHPDocTag {
	/**
	 * The var type
	 * @var string
	 */
	public $type;
	/**
	 * The constructor
	 * @param string $name The name of the tag
	 * @param string $param The parameter for the tag
	 */
	public function __construct($name, $param = null) {
		$this->tagName = $name;
		$segs=preg_split('/\s+/',$param,2);
		$this->type=array_shift($segs);
		$this->comment = array_shift($segs);
	}
}

/**
 * Holds information about a phpdoc property tag
 * @author Charles Pick
 * @package packages.docs.components
 */
class APHPDocPropertyTag extends APHPDocTag {
	/**
	 * The property type
	 * @var string
	 */
	public $type;
	
	/**
	 * The property name
	 * @var string
	 */
	public $name;
	
	/**
	 * The constructor
	 * @param string $name The name of the tag
	 * @param string $param The parameter for the tag
	 */
	public function __construct($name, $param = null) {
		$this->tagName = $name;
		$segs=preg_split('/\s+/',$param,3);
		$this->type=array_shift($segs);
		$this->name = substr(array_shift($segs),1);
		$this->comment = array_shift($segs);
	}
}

/**
 * Holds information about a phpdoc behavior tag
 * @author Charles Pick
 * @package packages.docs.components
 */
class APHPDocBehaviorTag extends APHPDocTag {
	/**
	 * The property type
	 * @var string
	 */
	public $type;
	
	/**
	 * The property name
	 * @var string
	 */
	public $name;
	
	/**
	 * The constructor
	 * @param string $name The name of the tag
	 * @param string $param The parameter for the tag
	 */
	public function __construct($name, $param = null) {
		$this->tagName = $name;
		$segs=preg_split('/\s+/',$param,3);
		$this->type=array_shift($segs);
		$this->name = substr(array_shift($segs),1);
		$this->comment = array_shift($segs);
	}
}

/**
 * Holds information about a phpdoc param tag
 * @author Charles Pick
 * @package packages.docs.components
 */
class APHPDocParamTag extends APHPDocTag {
	/**
	 * The param type
	 * @var string
	 */
	public $type;
	
	/**
	 * The param name
	 * @var string
	 */
	public $name;
	
	/**
	 * The constructor
	 * @param string $name The name of the tag
	 * @param string $param The parameter for the tag
	 */
	public function __construct($name, $param = null) {
		$this->tagName = $name;
		$segs=preg_split('/\s+/',$param,3);
		$this->type=array_shift($segs);
		$this->name = substr(array_shift($segs),1);
		$this->comment = array_shift($segs);
	}
}

/**
 * Holds information about a phpdoc return tag
 * @author Charles Pick
 * @package packages.docs.components
 */
class APHPDocReturnTag extends APHPDocTag {
	/**
	 * The return type
	 * @var string
	 */
	public $type;

	
	/**
	 * The constructor
	 * @param string $name The name of the tag
	 * @param string $param The parameter for the tag
	 */
	public function __construct($name, $param = null) {
		$this->tagName = $name;
		$segs=preg_split('/\s+/',$param,2);
		$this->type=array_shift($segs);
		$this->comment = array_shift($segs);
	}
}
