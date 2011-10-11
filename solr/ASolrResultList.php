<?php
/**
 * Holds a list of solr results
 * @author Charles Pick / PeoplePerHour.com
 * @package packages.solr
 */
class ASolrResultList extends CTypedList {
	/**
	 * Holds the total number of results
	 * @var integer
	 */
	public $total;

	/**
	 * Overrides the parent to specify the correct type
	 */
	public function __construct() {
		parent::__construct("ASolrDocument");
	}
}