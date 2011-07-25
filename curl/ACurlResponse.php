<?php
/**
 * Holds a curl response.
 * @author Charles Pick
 * @package packages.curl
 */
class ACurlResponse extends CComponent {
	/**
	 * The ACurl object that owns this curl response.
	 * @var ACurl
	 */
	public $request;
		
	/**
	 * The actual data returned by curl.
	 * @var string
	 */
	public $data;
	
	/**
	 * Holds the information returned by the CURL request.
	 * @see getInfo()
	 * @see setInfo()
	 * @var CAttributeCollection
	 */
	protected $_info;	
	
	/**
	 * Holds the header information returned by the CURL request.
	 * @see getHeaders()
	 * @see setHeaders()
	 * @var CTypedList
	 */
	protected $_headers;
		
	/**
	 * Returns information about the CURL response.
	 * @return CAttributeCollection the curl response details
	 */
	public function getInfo() {
		if ($this->_info === null) {
			$this->_info = new CAttributeCollection(curl_getinfo($this->request->getHandle()),true);
		}
		return $this->_info;
	}
	/**
	 * Sets the curl response details for this request.
	 * @param array $value The array for values from curl_getinfo
	 * @return ACurlResponse $this with the response details applied
	 */
	public function setInfo($value) {
		if (is_array($value)) {
			$this->_info = new CAttributeCollection($value,true);
		}
		elseif ($value instanceof CAttributeCollection) {
			$this->_info = $value;
		}
		else {
			throw new CException("ACurl::setInfo() - \$value must be an array or a CAttributeCollection");
		}
	}
	/**
	 * Gets the headers for this request.
	 * @return CTypedList a typed list of attribute
	 * collections representing each header.
	 */
	public function getHeaders() {
		return $this->_headers;
	}
	
	/**
	 * Sets the headers for this response.
	 * @param mixed $headers The headers to set, can be a string or an array
	 * @return ACurlResponse $this with the header details applied
	 */
	public function setHeaders($headers) {
		$this->_headers = new CTypedList("CAttributeCollection");
		if (is_string($headers)) {
			foreach(preg_split("/(\r\n){2,2}/",$headers,2) as $header) {
				if (trim($header) == "") {
					continue;
				}
				$item = array();
				$lines = explode("\r\n",$header);
				foreach($lines as $n => $line) {
					if ($n == 0) {
						$line = explode(" ",$line);
						$item['http_code'] = (int) trim($line[1]);
					}
					else {
						$key = mb_substr($line,0,mb_strpos($line,":"));
						if ($key == "") {
							continue;
						}
						$value = trim(mb_substr($line,mb_strlen($key) + 1));
						$item[$key] = $value;
					}
				}
				$this->_headers[] = new CAttributeCollection($item, true);
			}
			
		}
		elseif (is_array($headers)) {
			foreach($headers as $header) {
				$this->_headers[] = new CAttributeCollection($header, true);
			}
		}
		return $this; // chainable
	}
	
	/**
	 * Returns the response data when the object is cast to a string
	 * @return string The response data
	 */
	public function __toString() {
		return $this->data;
	}
	
	/**
	 * Decodes a JSON response
	 * @return mixed The decoded JSON object
	 */
	public function fromJSON() {
		if (function_exists("json_exists")) {
			return json_decode($this->data);
		}
		else {
			return CJSON::decode($this->data);
		}
		
	}
}
