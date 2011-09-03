<?php
/**
 * Allows a model to represent an item in a data feed.
 * @author Charles Pick
 * @package packages.feeds
 */
class AFeedable extends CActiveRecordBehavior {
	/**
	 * The db criteria to use when finding items to show in the RSS feed
	 * @var CDbCriteria
	 */
	public $criteria;
	/**
	 * The template for the item title.
	 * Values from the owner model can be included by wrapping
	 * the attribute name in {curly} brackets.
	 * @var string
	 */
	public $titleTemplate = "{title}";

	/**
	 * The template for the item title.
	 * Values from the owner model can be included by wrapping
	 * the attribute name in {curly} brackets.
	 * @var string
	 */
	public $descriptionTemplate = "{description}";
	/**
	 * The name of the attribute on the owner model that should be used to show
	 * @var string
	 */
	public $dateAttribute = "timeAdded";

	/**
	 * Named Scope: Gets items to show in the feed
	 * @return CActiveRecord $this->owner with the scope applied
	 */
	public function feedItems() {
		if ($this->criteria === null) {
			$criteria = new CDbCriteria;
			$criteria->order = $this->dateAttribute." DESC";

		}
		else {
			$criteria = clone $this->criteria;
		}
		$this->owner->getDbCriteria()->mergeWith($criteria);
		return $this->owner;
	}

	/**
	 * Gets the feed item for this model
	 * @return AFeedItem the feed item
	 */
	public function getFeedItem() {
		$item = new AFeedItem;
		$item->title = $this->processTemplate($this->titleTemplate);
		$item->description = $this->processTemplate($this->descriptionTemplate);
		$item->id = sha1(get_class($this->owner).":".$this->owner->primaryKey);
		$item->timePublished = $this->owner->{$this->dateAttribute};
		$item->url = $this->owner->createUrl(null,null,true);
		return $item;
	}
	/**
	 * Processes a template and replaces tokens with the attribute value from the owner model
	 * @param string $template the template to process
	 * @return string the processed template
	 */
	protected function processTemplate($template) {
		preg_match_all("/{(.*?)}/",$template,$matches);
		foreach($matches[1] as $n => $match) {
			if (strstr($match,":")) {
				$format = explode(":",$match);
				$attribute = array_shift($format);
				$format = array_shift($format);
				$template = str_replace($matches[0][$n],Yii::app()->format->{$format}($this->owner->{$attribute}),$template);
			}
			else {
				$template = str_replace($matches[0][$n],$this->owner->{$match},$template);
			}
		}
		return $template;
	}
}