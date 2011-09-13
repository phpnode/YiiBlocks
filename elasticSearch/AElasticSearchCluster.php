<?php
/**
 * Represents an Elastic Search cluster
 * @author Charles Pick
 * @package packages.elasticSearch
 */
class AElasticSearchCluster extends CComponent {
	/**
	 * The name of the cluster
	 * @var string
	 */
	public $name;

	/**
	 * The URL for this cluster
	 * @var string
	 */
	public $url;
	/**
	 * The name of the master node
	 * @var string
	 */
	public $masterNode;
	/**
	 * The nodes in this cluster
	 * @var array
	 */
	public $nodes = array();
	/**
	 * Constructor
	 * @param AElasticSearchResponse $response the elastic search response to initialize the results from
	 */
	public function __construct(AElasticSearchResponse $response = null) {
		$this->name = $response->cluster_name;
		$this->nodes = $response->nodes;
		$this->masterNode = $response->master_node;
	}
}