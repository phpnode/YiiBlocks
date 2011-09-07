<?php
/**
 * Holds a list of elastic search results
 * @author Charles Pick
 * @package packages.elasticSearch
 */
class AElasticSearchResultList extends CTypedList {
	/**
	 * Holds the total number of results
	 * @var integer
	 */
	public $total;

	/**
	 * Overrides the parent to specify the correct type
	 */
	public function __construct() {
		parent::__construct("AElasticSearchResult");
	}
}