<?php
/**
 * A simple wrapper for ElasticSearch
 * @author Charles Pick
 * @package packages.elasticSearch
 */
class AElasticSearchConnection extends CApplicationComponent {
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
	 * Whether to enable schema caching or not
	 * @var boolean
	 */
	public $enableSchemaCaching = false;

	/**
	 * The schema caching duration in seconds
	 * @var integer
	 */
	public $schemaCachingDuration = 60;

	/**
	 * Holds a list of indices
	 * @var CAttributeCollection
	 */
	protected $_indices;

	/**
	 * Gets a list of indices
	 * @return AElasticSearchResponse the list of indices, or false if there was a problem returning the list
	 */
	public function getIndices() {
		if ($this->_indices === null) {
			$url = $this->url."_status";
			$request = $this->makeRequest();
			if ($this->enableSchemaCaching) {
				$request->cache($this->schemaCachingDuration);
			}
			$this->_indices = AElasticSearchIndex::fromResponse($request->get($url)->exec(),$this);
		}
		return $this->_indices;
	}
	/**
	 * Lookup a list of types in an index
	 * @param AElasticSearchIndex $index the index to lookup
	 * @return AElasticSearchDocumentType[] an array of index types
	 */
	public function getIndexTypes(AElasticSearchIndex $index) {
		$request = $this->makeRequest();
		if ($this->enableSchemaCaching) {
			$request->cache($this->schemaCachingDuration);
		}
		$response = $request->get($this->url.$index->name."/_mapping")->exec();
		$types = new CAttributeCollection();
		$types->caseSensitive = true;
		if (!is_object($response->{$index->name})) {
			return $types;
		}
		foreach(array_keys($response->{$index->name}->toArray()) as $type) {
			$types[$type] = new AElasticSearchDocumentType($type,$index,$this);
		}
		return $types;
	}
	/**
	 * Makes a new elastic search request
	 * @return AElasticSearchRequest the prepared request
	 */
	public function makeRequest() {
		$request = new AElasticSearchRequest();
		$request->getOptions()->mergeWith($this->curlOptions);
		return $request;
	}

	/**
	 * Performs a search
	 * @param string $index The index to search in
	 * @param string $type The type of the object to find
	 * @param array $query The search criteria
	 * @return AElasticSearchResultList The search results or false if there is an error
	 */
	public function search($index, $type, $query = array()) {
		if ($index === null) {
			$index = $this->defaultIndex;
		}
		$url = $this->url.$index."/".$type."/_search";
		$request = $this->makeRequest();

		if (!is_array($query) && !($query instanceof AElasticSearchCriteria)) {

			try {
				$hits = $request->get($url."?q=".$this->encode($query))->exec()->hits;
			}
			catch (ACurlException $e) {
				return false;
			}
		}
		else {

			try {
				$hits = $request->post($url,$this->encode($query))->exec()->hits;
			}
			catch (ACurlException $e) {
				return false;
			}
		}

		$results = new AElasticSearchResultList();
		$results->total = $hits->total;
		$offset = 0;
		if ($query instanceof AElasticSearchCriteria) {
			$offset = $query->offset;
		}
		$n = 0;

		foreach($hits->hits as $hit) {
			$n++;
			$result = new AElasticSearchResult("update",$hit['_source']);
			$result->setIsNewRecord(false);
			$result->setScore($hit['_score']);
			$result->setId($hit['_id']);
			$result->setType($type);
			$result->setPosition($offset + $n);
			$results[] = $result;
		}
		return $results;
	}

	/**
	 * Returns the number of items that match a specific query
	 * @param string $index The index to search in
	 * @param string $type The type of the object to find
	 * @param array $query The search criteria
	 * @return array The search results or false if there is an error
	 */
	public function count($index, $type, $query = array()) {
		if ($query instanceof AElasticSearchCriteria) {
			$query = $query->toArray();
		}
		if (is_array($query)) {
			if (count($query) == 0) {
				$query = "*";
			}
			else {
				$query = array("query" => $query);
			}
		}



		if ($index === null) {
			$index = $this->defaultIndex;
		}
		$url = $this->url.$index."/".$type."/_count";
		$request = $this->makeRequest();
		try {
			if (is_array($query)) {
				return $request->post($url,$this->encode($query))->exec()->count;
			}
			else {
				return $request->get($url."?q=".$this->encode($query))->exec()->count;
			}
		}
		catch (ACurlException $e) {
			return false;
		}
	}
	/**
	 * Encodes a value to be transmitted to Elastic Search
	 * @param mixed $data the data to be encoded, if a string is given it will be urlencoded, otherwise it will be json encoded
	 * @return string the urlencoded or json encoded string, depending on the input
	 */
	protected function encode($data) {
		if (is_object($data) && method_exists($data,"toArray")) {
			$data = $data->toArray();
		}
		else if (!is_array($data)) {
			return urlencode($data);
		}
		if (function_exists("json_encode")) {
			$data = json_encode($data);
		}
		else {
			$data = CJSON::encode($data);
		}
		return $data;
	}

	/**
	 * Indexes a document
	 * @param string $index The index to use for this item
	 * @param string $type The type of the object to index
	 * @param mixed $id The ID of the item to index
	 * @param array $data The document data to store
	 * @return boolean true if the index succeeded
	 */
	public function index($index, $type, $id, $data) {

		if ($index === null) {
			$index = $this->defaultIndex;
		}
		if ($type instanceof AElasticSearchDocumentType) {
			$type = $type->name;
		}
		$url = $this->url.$index."/".$type."/".$id;
		$request = $this->makeRequest();
		try {
			return $request->post($url,$this->encode($data))->exec();
		}
		catch (ACurlException $e) {
			CVarDumper::dump($e,10,true);
			die();
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
			print_r($e->getMessage());
			die();
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
	 * @return boolean whether the delete succeeded or not
	 */
	public function delete($index, $type, $id) {
		if ($index === null) {
			$index = $this->defaultIndex;
		}
		if ($id instanceof AElasticSearchDocument) {
			$id = $id->getId();
		}
		$url = $this->url.$index;
		if ($type !== null) {
			$url .= "/".$type;
		}
		if ($id !== null) {
			$url .= "/".$id;
		}
		$request = $this->makeRequest();
		try {
			return $request->delete($url)->exec()->ok;
		}
		catch (ACurlException $e) {
			return false;
		}
	}

	public function getStatus() {
		$request = $this->makeRequest();
		$url = $this->url."_cluster/state";
		return $request->get($url)->exec();
	}

	public function getCluster() {
		$request = $this->makeRequest();
		$url = $this->url."_cluster/state";
		return new AElasticSearchCluster($request->get($url)->exec());
	}
}
