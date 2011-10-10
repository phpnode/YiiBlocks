<?php
/**
 * A wrapper that provides easy access to curl functions.
 * @author Charles Pick
 * @package packages.curl
 */
class ACurl extends CComponent {
	/**
	 * Holds the options to use for this request
	 * @see getOptions
	 * @see setOptions
	 * @var ACurlOptions
	 */
	protected $_options;

	/**
	 * Holds the curl handle
	 * @var resource
	 */
	protected $_handle;

	/**
	 * Holds the cache key
	 * @var string
	 */
	protected $_cacheKey;

	/**
	 * The caching duration
	 * @var integer
	 */
	protected $_cacheDuration;

	/**
	 * The cache dependency
	 * @var CCacheDependency
	 */
	protected $_cacheDependency;

	/**
	 * The cache component to use when caching results
	 * @var CCache
	 */
	protected $_cacheComponent;

	/**
	 * Whether to cache the query result or not.
	 * Defaults to false.
	 * @var boolean
	 */
	protected $_cache = false;

	/**
	 * Returns the CURL handle for this request
	 * @var resource
	 */
	public function getHandle() {
		if ($this->_handle === null) {
			$this->_handle = curl_init();
		}
		return $this->_handle;
	}

	/**
	 * Sets the curl handle for this request.
	 * @param resource $value The CURL handle
	 * @return ACurl $this with the handle set.
	 */
	public function setHandle($value) {
		$this->_handle = $value;
		return $this;
	}

	/**
	 * Gets the options to use for this request.
	 * @return ACurlOptions the options
	 */
	public function getOptions() {
		if ($this->_options === null) {
			$this->_options = new ACurlOptions(array(
				"userAgent" => "Yii PHP Framework / ACurl",
				"header" => true,
				"followLocation" => true,
				"returnTransfer" => true,
				"failOnError" => true,
				"timeout" => 30,
				"encoding" => "gzip",
				"ssl_verifypeer" => false,


			));
		}
		return $this->_options;
	}

	/**
	 * Sets the options to the given value.
	 * @param mixed $value the options, either an array or an ACurlOptions object
	 * @return ACurl $this with the modified options
	 */
	public function setOptions($value) {
		if (is_array($value)) {
			$value = new ACurlOptions($value);
		}
		$this->_options = $value;
		return $this;
	}

	/**
	 * Prepares the CURL request, applies the options to the handler.
	 */
	public function prepareRequest() {
		$this->getOptions()->applyTo($this);

	}

	/**
	 * Sets the post data and the URL to post to and prepares the request
	 * but does not actually perform the POST, exec() should be called to
	 * perform the actual request.
	 * @param string $url The URL to post to.
	 * @param array $data The data to post key=>value
	 * @return ACurl $this with the POST and URL settings applied
	 */
	public function post($url, $data = array()) {
		$this->getOptions()->url = $url;
		$this->getOptions()->postfields = $data;
		$this->getOptions()->post = true;
		$this->prepareRequest();
		return $this;
	}


	/**
	 * Sets the PUT data and the URL to PUT to and prepares the request
	 * but does not actually perform the PUT, exec() should be called to
	 * perform the actual request.
	 * @param string $url The URL to PUT to.
	 * @param array $data The data to PUT key=>value
	 * @return ACurl $this with the PUT and URL settings applied
	 */
	public function put($url, $data = array()) {
		$this->getOptions()->url = $url;
		$this->getOptions()->postfields = $data;
		$this->getOptions()->post = false;
		$this->getOptions()->customRequest = "PUT";
		$this->prepareRequest();
		return $this;
	}

	/**
	 * Sets the DELETE data and the URL to DELETE to and prepares the request
	 * but does not actually perform the DELETE, exec() should be called to
	 * perform the actual request.
	 * @param string $url The URL to DELETE to.
	 * @return ACurl $this with the DELETE and URL settings applied
	 */
	public function delete($url) {
		$this->getOptions()->url = $url;
		$this->getOptions()->customRequest = "DELETE";
		$this->prepareRequest();
		return $this;
	}


	/**
	 * Sets the URL and prepares the GET request
	 * but does not actually perform the GET, exec() should be called to
	 * perform the actual request.
	 * @param string $url The URL to get.
	 * @return ACurl $this with the URL settings applied
	 */
	public function get($url) {
		$this->getOptions()->url = $url;

		$this->getOptions()->post = false;
		$this->prepareRequest();
		return $this;
	}
	/**
	 * Sets the URL and prepares the HEAD request
	 * but does not actually perform the HEAD, exec() should be called to
	 * perform the actual request.
	 * @param string $url The URL to post to.
	 * @return ACurl $this with the URL and relevant curl settings applied
	 */
	public function head($url) {
		$this->getOptions()->url = $url;
		$this->getOptions()->nobody = true;
		$this->prepareRequest();
		return $this;
	}

	/**
	 * Executes the request and returns the response.
	 * @return ACurlResponse the wrapped curl response
	 */
	public function exec() {
		$response = new ACurlResponse;
		$response->request = $this;
		$data = false;
		$cache = $this->_cache;
		if ($this->getOptions()->itemAt("post") || $this->getOptions()->itemAt("customRequest")) {
			$cache = false;
		}
		if ($cache) {
			$data = $this->getCacheComponent()->get($this->getCacheKey());
		}
		if ($data === false) {
			$data = curl_exec($this->getHandle());
			if ($cache) {
				$this->getCacheComponent()->set($this->getCacheKey(),$this->_cacheDuration,$this->_cacheDependency);
			}
		}
		$response->data = $data;

		if ($this->getOptions()->header) {
			$response->headers = mb_substr($response->data, 0, $response->info->header_size);
			$response->data = mb_substr($response->data, $response->info->header_size);
			if (mb_strlen($response->data) == 0) {
				$response->data = false;
			}
		}
		if (curl_error($this->getHandle())) {
			throw new ACurlException(curl_errno($this->getHandle()),curl_error($this->getHandle()), $response);
		}

		return $response;
	}

	/**
	 * Enables caching for curl requests
	 * @param integer $duration the caching duration
	 * @param CCacheDependency $dependency the cache dependency for this request
	 * @return ACurl $this with the cache setting applied
	 */
	public function cache($duration = 60, $dependency = null) {
		$this->_cache = true;
		$this->_cacheDuration = $duration;
		$this->_cacheDependency = $dependency;
		return $this;
	}

	/**
	 * Sets the cache component to use for this request
	 * @param CCache $cacheComponent the cache component
	 */
	public function setCacheComponent($cacheComponent) {
		$this->_cacheComponent = $cacheComponent;
	}

	/**
	 * Gets the cache component for this curl request
	 * @return CCache the caching component to use for this request
	 */
	public function getCacheComponent() {
		if ($this->_cacheComponent === null) {
			$this->_cacheComponent = Yii::app()->getCache();
		}
		return $this->_cacheComponent;
	}

	/**
	 * Sets the cache key for this request
	 * @param string $cacheKey the cache key
	 * @return string the cache key
	 */
	public function setCacheKey($cacheKey) {
		return $this->_cacheKey = $cacheKey;
	}

	/**
	 * Gets the cache key for this request
	 * @return string the cache key
	 */
	public function getCacheKey() {
		if ($this->_cacheKey === null) {
			$this->_cacheKey = "ACurl:cachedRequest:".sha1(serialize($this->getOptions()->toArray()));
		}
		return $this->_cacheKey;
	}

}
