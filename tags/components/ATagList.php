<?php
/**
 * Holds a list of tags
 * @author Charles Pick
 * @package packages.tags
 */
class ATagList extends CTypedList {
	/**
	 * The separator to use when joining a list of tags, defaults to ,
	 * @see __toString()
	 * @var string
	 */
	public $separator = ",";
	/**
	 * An array of tag ids that have been added to the list
	 * @var array
	 */
	public $added = array();
	/**
	 * An array of tag ids that have been removed from the list
	 * @var array
	 */
	public $removed = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct("ATag");
	}

	/**
	 * Loads tag list contents from a string
	 * @param string $input the separated list of tags
	 * @return ATagList the list of tags
	 */
	public function fromString($input) {
		$rawTags = preg_split("/[\s]*[".$this->separator."][\s]*/", $input);
		foreach($this->toArray() as $tag) {
			$matched = false;
			foreach($rawTags as $j => $rawTag) {
				if (strcasecmp($rawTag,$tag->tag) === 0) {
					$matched = true;
					unset($rawTags[$j]);
					break;
				}
			}
			if (!$matched) {
				$this->removed[] = $tag->id;
				$this->remove($tag);
			}

		}

		$modelClass = Yii::app()->getModule("tags")->tagModelClass;
		// now add the tags that are new
		foreach($rawTags as $tagName) {
			$tag = $modelClass::model()->findByAttributes(array("tag" => $tagName));
			if (!is_object($tag)) {
				$tag = new $modelClass;
				$tag->tag = $tagName;
				$tag->save();
			}
			$this->added[] = $tag->id;
			$this[] = $tag;
		}

		return $this;
	}
	/**
	 * Gets a string representation of this tag list
	 * @return string the tags, separated by $this->separator
	 */
	public function __toString() {
		$tags = array();
		foreach($this as $tag) {
			$tags[] = $tag->tag;
		}
		return implode($this->separator,$tags);
	}

}