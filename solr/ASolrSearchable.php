<?php
/**
 * Allows easy indexing and searching for Active Records
 * @package packages.solr
 * @author Charles Pick / PeoplePerHour.com
 */
class ASolrSearchable extends CActiveRecordBehavior {
	/**
	 * The class name of the solr document to instantiate
	 * @var string
	 */
	public $documentClass = "ASolrDocument";
	/**
	 * The solr document associated with this model instance
	 * @var ASolrDocument
	 */
	protected $_solrDocument;

	/**
	 * The solr criteria associated with this model instance
	 * @var ASolrCriteria
	 */
	protected $_solrCriteria;

	/**
	 * The attributes that should be indexed in solr
	 * @var array
	 */
	protected $_attributes;

	/**
	 * Sets the attributes that should be indexed in solr
	 * @param array $attributes
	 */
	public function setAttributes($attributes)
	{
		$this->_attributes = $attributes;
	}

	/**
	 * Gets the attributes that should be indexed in solr
	 * @return array
	 */
	public function getAttributes()
	{
		if ($this->_attributes === null) {
			$this->_attributes = $this->getOwner()->attributeNames();
		}
		return $this->_attributes;
	}

	/**
	 * Sets the solr document associated with this model instance
	 * @param ASolrDocument $solrDocument the solr document
	 */
	public function setSolrDocument($solrDocument)
	{
		$this->_solrDocument = $solrDocument;
	}

	/**
	 * Gets the solr document associated with this model instance.
	 * @param boolean $refresh whether to refresh the document, defaults to false
	 * @return ASolrDocument the solr document
	 */
	public function getSolrDocument($refresh = false)
	{
		if ($this->_solrDocument === null || $refresh) {
			$className = $this->documentClass;
			$this->_solrDocument = new $className();
			foreach($this->getAttributes() as $attribute) {
				$this->_solrDocument->{$attribute} = $this->getOwner()->{$attribute};
			}
		}
		return $this->_solrDocument;
	}

	/**
	 * Adds the solr document to the index
	 * @return boolean true if the document was indexed successfully
	 */
	public function index() {
		$document = $this->getSolrDocument(true);
		return $document->save();

	}
	/**
	 * Resets the scope
	 * @return ASolrSearchable $this with the scope reset
	 */
	public function resetScope() {
		$this->_solrCriteria = null;
		return $this;
	}

	/**
	 * Sets the solr criteria associated with this model
	 * @param ASolrCriteria $solrCriteria the solr criteria
	 */
	public function setSolrCriteria($solrCriteria)
	{
		$this->_solrCriteria = $solrCriteria;
	}

	/**
	 * Gets the solr criteria associated with this model
	 * @return ASolrCriteria the solr criteria
	 */
	public function getSolrCriteria()
	{
		if ($this->_solrCriteria === null) {
			$this->_solrCriteria = new ASolrCriteria();
		}
		return $this->_solrCriteria;
	}
}
