<?php
/**
 * Represents a solr query facet.
 *
 * Each result in the facet can be accessed by using the following syntax:
 * <pre>
 * foreach($facet as $key => $value) {
 * 	echo $value." result(s) for ".$key."\n";
 * </pre>
 * @author Charles Pick / PeoplePerHour.com
 * @package packages.solr
 */
class ASolrFacet extends CMap {
	const TYPE_QUERY = "facet_queries";
	const TYPE_FIELD = "facet_fields";
	const TYPE_DATE = "facet_dates";
	const TYPE_RANGE = "facet_ranges";
	/**
	 * The name of the facet
	 * @var string
	 */
	public $name;

	/**
	 * The type of the facet
	 * @var string
	 */
	public $type;

	/**
	 * The solr query response this facet belongs to
	 * @var ASolrQueryReseponse
	 */
	public $response;
}