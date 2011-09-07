<?php
/**
 * Represents an elastic search index
 * @author Charles Pick
 * @package packages.elasticSearch
 */
class AElasticSearchIndex extends CFormModel {

	/**
	 * The name of the index
	 * @var string
	 */
	public $name;
	/**
	 * The size of this index in bytes
	 * @var integer
	 */
	public $sizeInBytes;

	/**
	 * The human friendly index size
	 * @var string
	 */
	public $size;

	/**
	 * The total number of documents
	 * @var integer
	 */
	public $totalDocuments = 0;

	protected $_types;

	/**
	 * The elastic search connection
	 * @var AElasticSearch
	 */
	protected $connection;

	/**
	 * Loads a list of search indexes from an elastic search response
	 * @param AElasticSearchResponse $response the response from elastic search
	 * @param AElasticSearch $connection the elastic search connection
	 * @return CAttributeCollection the indexes
	 */
	public static function fromResponse(AElasticSearchResponse $response, AElasticSearch $connection) {
		$indices = new CAttributeCollection();
		foreach($response->indices as $name => $index) {
			$item = new AElasticSearchIndex();
			$item->name = $name;
			$item->size = $index->index->size;
			$item->sizeInBytes = $index->index->size_in_bytes;
			$item->totalDocuments = $index->docs->num_docs;
			$item->connection = $connection;
			$indices[$name] = $item;

		}
		return $indices;

	}
	/**
	 * @return
	 */
	public function getTypes() {
		if ($this->_types === null) {
			$this->_types = $this->connection->getIndexTypes($this);
		}
		return $this->_types;
	}
	/**
	 * Makes an elastic search
	 * @param AElasticSearchCriteria|null $criteria the search query
	 * @param AElasticSearchDocumentType|string $type the type to search
	 * @return array the search results
	 */
	public function search(AElasticSearchCriteria $criteria = null, $type = null) {
		if (!($type instanceof AElasticSearchDocumentType)) {
			$type = new AElasticSearchDocumentType($type,$this,$this->connection);
		}
		return $this->connection->search($this->name,$type,$criteria);
	}

	/**
	 * Counts the results from an elastic search query
	 * @param AElasticSearchCriteria|null $criteria the search query
	 * @param AElasticSearchDocumentType|string $type the type to search
	 * @return integer the number of results
	 */
	public function count(AElasticSearchCriteria $criteria = null, $type = null) {
		if (!($type instanceof AElasticSearchDocumentType)) {
			$type = new AElasticSearchDocumentType($type,$this,$this->connection);
		}
		return $this->connection->count($this->name,$type,$criteria);
	}
	/**
	 * @return string the index name
	 */
	public function __toString() {
		return $this->name;
	}
}