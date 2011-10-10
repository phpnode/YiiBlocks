<?php
/**
 * Wraps a solr query response
 * @author Charles Pick / PeoplePerHour.com
 * @package packages.solr
 */
class ASolrQueryResponse extends CComponent {
	/**
	 * Holds the solr query response
	 * @var SolrObject
	 */
	protected $_solrObject;

	/**
	 * Holds the solr criteria that caused the results
	 * @var ASolrCriteria
	 */
	protected $_criteria;
	/**
	 * The search results
	 * @var ASolrResultList
	 */
	protected $_results;

	/**
	 * A collection of query facets
	 * @var CAttributeCollection
	 */
	protected $_queryFacets;
	/**
	 * A collection of field facets
	 * @var CAttributeCollection
	 */
	protected $_fieldFacets;
	/**
	 * A collection of date facets
	 * @var CAttributeCollection
	 */
	protected $_dateFacets;
	/**
	 * A collection of range facets
	 * @var CAttributeCollection
	 */
	protected $_rangeFacets;
	/**
	 * Constructor.
	 * @param SolrObject $solrObject the response from solr
	 * @param ASolrCriteria $criteria the search criteria
	 */
	public function __construct($solrObject, ASolrCriteria $criteria) {
		$this->_solrObject = $solrObject;
		$this->_criteria = $criteria;
	}

	/**
	 * Gets an array of date facets that belong to this query response
	 * @return ASolrFacet[]
	 */
	public function getDateFacets()
	{
		if ($this->_dateFacets === null) {
			$this->loadFacets();
		}
		return $this->_dateFacets;
	}

	/**
	 * Gets an array of field facets that belong to this query response
	 * @return ASolrFacet[]
	 */
	public function getFieldFacets()
	{
		if ($this->_fieldFacets === null) {
			$this->loadFacets();
		}
		return $this->_fieldFacets;
	}
	/**
	 * Gets an array of query facets that belong to this query response
	 * @return ASolrFacet[]
	 */
	public function getQueryFacets()
	{
		if ($this->_queryFacets === null) {
			$this->loadFacets();
		}
		return $this->_queryFacets;
	}
	/**
	 * Gets an array of range facets that belong to this query response
	 * @return ASolrFacet[]
	 */
	public function getRangeFacets()
	{
		if ($this->_rangeFacets === null) {
			$this->loadFacets();
		}
		return $this->_rangeFacets;
	}
	/**
	 * Loads the facet objects
	 */
	protected function loadFacets() {
		$this->_dateFacets = new CAttributeCollection();
		$this->_dateFacets->caseSensitive = true;
		$this->_fieldFacets = new CAttributeCollection();
		$this->_fieldFacets->caseSensitive = true;
		$this->_queryFacets = new CAttributeCollection();
		$this->_queryFacets->caseSensitive = true;
		$this->_rangeFacets = new CAttributeCollection();
		$this->_rangeFacets->caseSensitive = true;
		if (!isset($this->getSolrObject()->facet_counts)) {
			return false;
		}
		foreach($this->getSolrObject()->facet_counts as $facetType => $item) {
			foreach($item as $facetName => $values) {
				if (!is_array($values)) {
					$values = array("value" => $values);
				}
				$facet = new ASolrFacet($values);
				$facet->name = $facetName;
				$facet->type = $facetType;

				switch ($facetType) {
					case ASolrFacet::TYPE_DATE:
						$this->_dateFacets[$facet->name] = $facet;
						break;
					case ASolrFacet::TYPE_FIELD:
						$this->_fieldFacets[$facet->name] = $facet;
						break;
					case ASolrFacet::TYPE_QUERY:
						$this->_queryFacets[$facet->name] = $facet;
						break;
					case ASolrFacet::TYPE_RANGE:
						$this->_rangeFacets[$facet->name] = $facet;
						break;
				}
			}
		}
	}


	/**
	 * Gets the SolrObject object wrapped by this class
	 * @return SolrObject the solr query response object
	 */
	public function getSolrObject()
	{
		return $this->_solrObject;
	}

	/**
	 * Gets the list of search results
	 * @return ASolrResultList the solr results
	 */
	public function getResults()
	{
		#return $this->_solrObject;
		if ($this->_results === null) {
			$this->_results = new ASolrResultList;
			$this->_results->total = $this->_solrObject->response->numFound;
			foreach($this->_solrObject->response->docs as $n => $row) {
				$result = new ASolrResult();
				foreach($row as $attribute => $value) {
					$result->{$attribute} = $value;
				}
				$result->setPosition($n + $this->_criteria->getOffset());
				$this->_results->add($result);
			}
		}
		return $this->_results;
	}
}