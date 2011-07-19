<?php
/**
 * A simple wrapper for ElasticSearch
 * @author Charles Pick
 * @package blocks.elasticSearch
 */
class AElasticSearch extends CApplicationComponent {
	/**
	 * The base URL of the the elastic search instance including port,
	 * defaults to "http://localhost:9200/" 
	 * @var string
	 */
	public $url = "http://localhost:9200/";
	
	/**
	 * The default index to use when none is specified,
	 * defaults to "main".
	 * @var string
	 */
	public $defaultIndex = "main";
	
	/**
	 * An array of CURL options that should be applied to each CURL request.
	 * These will be merged with the default curl options.
	 * @see ACurlOptions
	 * @var array
	 */
	public $curlOptions = array();
	
	/**
	 * Performs a search
	 * @param string $index The index to search in
	 * @param string $type The type of the object to find
	 * @param array $query The search criteria
	 * @return array The search results or false if there is an error
	 */
	public function search($index, $type, $query = array()) {
		if ($index === null) {
			$index = $this->defaultIndex;
		}
		$url = $this->url.$index."/".$type."/_search";
		$curl = new ACurl();
		$curl->getOptions()->mergeWith($this->curlOptions);
		if (!is_array($query)) {
			
			try {
				return $curl->get($url."?q=".urlencode($query))->exec()->fromJSON();
			}
			catch (ACurlException $e) {
				return false;
			}
		}
		else {
			if (function_exists("json_encode")) {
				$query = json_encode($query);
			}
			else {
				$query = CJSON::encode($query);
			}
		
			try {
				return $curl->post($url,$query)->exec()->fromJSON();
			}
			catch (ACurlException $e) {
				return false;
			}
		}
	}
	
	/**
	 * Returns the number of items that match a specific query
	 * @param string $index The index to search in
	 * @param string $type The type of the object to find
	 * @param array $query The search criteria
	 * @return array The search results or false if there is an error
	 */
	public function count($index, $type, $query = array()) {
		$data = array("query" => $query);
		if (function_exists("json_encode")) {
			$data = json_encode($data);
		}
		else {
			$data = CJSON::encode($data);
		}
		if ($index === null) {
			$index = $this->defaultIndex;
		}
		$url = $this->url.$index."/".$type."/_count";
		$curl = new ACurl();
		$curl->getOptions()->mergeWith($this->curlOptions);
		try {
			return $curl->post($url,$data)->exec()->fromJSON();
		}
		catch (ACurlException $e) {
			return false;
		}
	}
	
	
	/**
	 * Indexes a document
	 * @param string $index The index to use for this item
	 * @param string $type The type of the object to index
	 * @param mixed $id The ID of the item to index
	 * @param array $data The document data to store
	 * @return array The response from the server or false if there was an error
	 */
	public function index($index, $type, $id, $data) {
		
		if (function_exists("json_encode")) {
			$data = json_encode($data);
		}
		else {
			$data = CJSON::encode($data);
		}
		if ($index === null) {
			$index = $this->defaultIndex;
		}
		$url = $this->url.$index."/".$type."/".$id;
		$curl = new ACurl();
		$curl->getOptions()->mergeWith($this->curlOptions);
		try {
			return $curl->post($url,$data)->exec()->fromJSON();
		}
		catch (ACurlException $e) {
			return false;
		}
	}
	
	/**
	 * Sets the mapping for a particular index
	 * @param string $index The index to use
	 * @param string $type The type of the object to index
	 * @param array $data The mapping
	 * @return array The response from the server or false if there was an error
	 */
	public function putMapping($index, $type, $data) {
		if (function_exists("json_encode")) {
			$data = json_encode($data);
		}
		else {
			$data = CJSON::encode($data);
		}
		
		if ($index === null) {
			$index = $this->defaultIndex;
		}
		$url = $this->url.$index."/".$type."/_mapping";
		$curl = new ACurl();
		$curl->getOptions()->mergeWith($this->curlOptions);
		try {
			return $curl->post($url,$data)->exec()->fromJSON();
		}
		catch (ACurlException $e) {
			print_r($e->response);
			return false;
		}
		
	}
	
	
	/**
	 * Deletes the mapping for a particular index
	 * @param string $index The index to use
	 * @param string $type The type of the object to index
	 * @return array The response from the server or false if there was an error
	 */
	public function deleteMapping($index, $type) {
	
		
		if ($index === null) {
			$index = $this->defaultIndex;
		}
		$url = $this->url.$index."/".$type;
		$curl = new ACurl();
		$curl->getOptions()->mergeWith($this->curlOptions);
		try {
			return $curl->delete($url)->exec()->fromJSON();
		}
		catch (ACurlException $e) {
			print_r($e->response);
			return false;
		}
		
	}
	
	
	/**
	 * Deletes a document from elastic search
	 * @param string $index The index to use for this item
	 * @param string $type The type of the object to index
	 * @param mixed $id The ID of the item to index
	 * @param array $data The document data to store
	 * @return array The response from the server or false if there was an error
	 */
	public function delete($index, $type, $id) {
		if ($index === null) {
			$index = $this->defaultIndex;
		}
		$url = $this->url.$index."/".$type."/".$id;
		$curl = new ACurl();
		$curl->getOptions()->mergeWith($this->curlOptions);
		try {
			return $curl->delete($url)->exec()->fromJSON();
		}
		catch (ACurlException $e) {
			return false;
		}
	}
}
