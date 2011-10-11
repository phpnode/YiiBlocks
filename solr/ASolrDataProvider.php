<?php
/**
 * A data provider that obtains data from solr
 * @author Charles Pick / PeoplePerHour.com
 * @package packages.solr
 */
class ASolrDataProvider extends CActiveDataProvider {
	/**
	 * Holds the key attribute
	 * @var string
	 */
	public $keyAttribute = "position";

	/**
	 * Holds the response from solr
	 * @var ASolrQueryResponse
	 */
	protected $_solrQueryResponse;

	/**
	 * The solr criteria
	 * @var ASolrCriteria
	 */
	protected $_criteria;
	/**
	 * Constructor.
	 * @param mixed $modelClass the model class (e.g. 'Post') or the model finder instance
	 * (e.g. <code>Post::model()</code>, <code>Post::model()->published()</code>).
	 * @param array $config configuration (name=>value) to be applied as the initial property values of this class.
	 */
	public function __construct($modelClass,$config=array())
	{
		if($modelClass instanceof ASolrDocument) {
			$this->modelClass=get_class($modelClass);
			$this->model=$modelClass;

			$this->setId($this->modelClass);
			foreach($config as $key=>$value) {
				$this->$key=$value;
			}
		}
		else {
			$this->modelClass=$modelClass;
			$this->model=ASolrDocument::model($this->modelClass);
		}
		$this->setId($this->modelClass);
		foreach($config as $key=>$value) {
			$this->$key=$value;
		}
	}
	/**
	 * Returns the query criteria.
	 * @return ASolrCriteria the query criteria
	 */
	public function getCriteria()
	{
		if($this->_criteria===null)
			$this->_criteria=new ASolrCriteria();
		return $this->_criteria;
	}

	/**
	 * Sets the query criteria.
	 * @param mixed $value the query criteria. This can be either a ASolrCriteria object or an array
	 * representing the query criteria.
	 */
	public function setCriteria($value)
	{
		$this->_criteria=$value instanceof ASolrCriteria ? $value : new ASolrCriteria($value);
	}
	/**
	 * Fetches the data from the persistent data storage.
	 * @return array list of data items
	 */
	protected function fetchData()
	{
		$criteria=new ASolrCriteria();
		$criteria->mergeWith($this->getCriteria());

		if(($pagination=$this->getPagination())!==false)
		{
			$pagination->setItemCount($this->getTotalItemCount());
			$pagination->applyLimit($criteria);
		}

		$data=$this->model->findAll($criteria);
		$this->_solrQueryResponse = $this->model->getSolrConnection()->getLastQueryResponse();

		if ($pagination !== false) {
			$pagination->setItemCount(count($data));
		}
		return $data;
	}

	/**
	 * Calculates the total number of data items.
	 * @return integer the total number of data items.
	 */
	protected function calculateTotalItemCount()
	{
		return $this->model->count($this->getCriteria());
	}

	/**
	 * Gets an array of date facets that belong to this query response
	 * @return ASolrFacet[]
	 */
	public function getDateFacets()
	{
		if ($this->_solrQueryResponse === null) {
			$this->getData();
		}
		return $this->_solrQueryResponse->getDateFacets();
	}

	/**
	 * Gets an array of field facets that belong to this query response
	 * @return ASolrFacet[]
	 */
	public function getFieldFacets()
	{
		if ($this->_solrQueryResponse === null) {
			$this->getData();
		}
		return $this->_solrQueryResponse->getFieldFacets();
	}
	/**
	 * Gets an array of query facets that belong to this query response
	 * @return ASolrFacet[]
	 */
	public function getQueryFacets()
	{
		if ($this->_solrQueryResponse === null) {
			$this->getData();
		}
		return $this->_solrQueryResponse->getQueryFacets();
	}
	/**
	 * Gets an array of range facets that belong to this query response
	 * @return ASolrFacet[]
	 */
	public function getRangeFacets()
	{
		if ($this->_solrQueryResponse === null) {
			$this->getData();
		}
		return $this->_solrQueryResponse->getRangeFacets();
	}
}