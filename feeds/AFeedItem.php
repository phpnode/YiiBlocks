<?php
/**
 * Represents an item in a data feed.
 * @author Charles Pick
 * @package packages.feeds
 */
class AFeedItem extends CComponent {
	/**
	 * The unique id for this feed item
	 * @var string
	 */
	public $id;
	/**
	 * The title for this feed item
	 * @var string
	 */
	public $title;
	/**
	 * The description for this feed item
	 * @var string
	 */
	public $description;
	/**
	 * The url for this item
	 * @var string
	 */
	public $url;
	/**
	 * The (unix) time the item was published
	 * @var integer
	 */
	protected $_timePublished;

	/**
	 * Sets the time the item was published
	 * @param mixed $timePublished the time the item was published, this will be converted to unix time if it is not already
	 */
	public function setTimePublished($timePublished) {

		if (is_string($timePublished)) {
			$timePublished = strtotime($timePublished);
		}
		$this->_timePublished = $timePublished;
	}

	/**
	 * Gets the time the item was published
	 * @return integer the unix time the item was published
	 */
	public function getTimePublished() {
		return $this->_timePublished;
	}
}