<?php
/**
 * Holds meta data for an elastic search record
 *
 * @author Charles Pick
 * @package packages.elasticSearch
 */
class AElasticRecordMetaData {
	/**
	 * The elastic search document type
	 * @var AElasticSearchDocumentType
	 */
	public $type;
	/**
	 * Holds a list of relations
	 * @var array
	 */
	public $relations = array();

	/**
	 * Holds the attribute default values
	 * @var array
	 */
	public $attributeDefaults = array();

	/**
	 * Holds the model instance
	 * @var AElasticRecord
	 */
	protected $_model;

	/**
	 * Constructor
	 * @param AElasticRecord $model the model instance
	 */
	public function __construct(AElasticRecord $model) {
		$this->_model = $model;
		$connection = $model->getDbConnection();
		$indexName = $model->indexName();
		$indices = $connection->getIndices();
		if (!isset($indices[$indexName])) {
			$indices[$indexName] = new AElasticSearchIndex();
			$model->type();
		}

	}

}