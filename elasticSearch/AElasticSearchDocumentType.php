<?php
/**
 * Represents an elastic search document type
 * @author Charles Pick
 * @package packages.elasticSearch
 */
class AElasticSearchDocumentType extends CComponent {

	/**
	 * The name of the document type
	 * @var string
	 */
	public $name;

	/**
	 * The elastic search index this type belongs to
	 * @var AElasticSearchIndex
	 */
	public $index;

	/**
	 * The elastic search connection
	 * @var AElasticSearch
	 */
	protected $connection;

	protected $_dataProvider;
	/**
	 * Constructor, initializes the document type
	 * @param string $name the name of the document type
	 * @param AElasticSearchIndex $index the index the type belongs to
	 * @param AElasticSearch $connection the connection for the index
	 */
	public function __construct($name, AElasticSearchIndex $index, AElasticSearch $connection) {
		$this->name = (string) $name;
		$this->index = $index;
		$this->connection = $connection;
	}
	/**
	 * Gets the data provider for this document type
	 * @return AElasticSearchDataProvider the data provider for this doc type
	 */
	public function getDataProvider() {
		if ($this->_dataProvider === null) {
			$this->_dataProvider = new AElasticSearchDataProvider($this);
		}
		return $this->_dataProvider;
	}
	/**
	 * Sets the data provider for this type
	 * @param AElasticSearchDataProvider $dataProvider the data provider
	 */
	public function setDataProvider(AElasticSearchDataProvider $dataProvider) {
		$this->_dataProvider = $dataProvider;
	}
	/**
	 * @param AElasticSearchCriteria $criteria the elastic search criteria
	 */
	public function search($criteria = null) {
		return $this->index->search($criteria,$this);
	}

	/**
	 * @param AElasticSearchCriteria $criteria the elastic search criteria
	 */
	public function count($criteria = null) {
		return $this->index->count($criteria,$this);
	}
	/**
	 * Adds a document to the index
	 * @param AElasticSearchDocument $document the document to index
	 * @return array the index results
	 */
	public function index(AElasticSearchDocument $document) {
		return $this->index->index($document,$this);
	}
	/**
	 * Removes type or document from the index.
	 * If no document is specified the whole type will be deleted!
	 * @param AElasticSearchDocument $document the document to delete, if null the type will be deleted
	 * @return array the index results
	 */
	public function delete(AElasticSearchDocument $document = null) {
		return $this->index->delete($document,$this);
	}
	/**
	 * Converts the object to the type name
	 * @return string the type name
	 */
	public function __toString() {

		return $this->name;
	}
}