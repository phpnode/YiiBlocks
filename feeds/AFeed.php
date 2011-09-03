<?php
/**
 * A base class for data feeds.
 * @package packages.feeds
 * @author Charles Pick
 */
class AFeed extends CTypedList {
	/**
	 * Constructor, initializes the list type
	 */
	public function __construct() {
		parent::__construct("AFeedItem");
	}
}