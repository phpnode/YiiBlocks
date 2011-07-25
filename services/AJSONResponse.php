<?php
/**
 * Holds information about a JSON service response.
 * This is useful when responding to requests with JSON, e.g.
 * <pre>
 * $response = new AJSONResponse();
 * $response->welcomeString = "Hello World";
 * $response->anotherField = "Something Else";
 * $response->render(); // sends the headers and renders the JSON
 * </pre>
 * 
 * JSONP is also supported, e.g.
 * <pre>
 * $response = new AJSONResponse();
 * $response->setJSONP(true);
 * $response->setCallback("someFunc");
 * $response->welcomeString = "Hello World";
 * $response->anotherField = "Something Else";
 * $response->render(); // sends the headers and renders the JSONP with a callback called "someFunc"
 * </pre>
 * @author Charles Pick
 * @package packages.services
 */
class AJSONResponse extends AServiceResponse {
	/**
	 * Whether to use JSONP or not, defaults to false.
	 * @see getJSONP()
	 * @see setJSONP()
	 * @var boolean
	 */
	protected $_JSONP = false;
	/**
	 * The name of the callback function to use when _JSONP is true.
	 * Defaults to "callback".
	 * @var string
	 */
	protected $_callback = "callback";
	
	/**
	 * Gets whether to use JSONP or not, defaults to false.
	 * @return boolean whether to use JSONP or not.
	 */
	public function getJSONP() {
		return $this->_JSONP;
	}
	/**
	 * Sets whether to use JSONP or not.
	 * @param boolean $value whether to use JSONP or not
	 */
	public function setJSONP($value) {
		return $this->_JSONP = $value;
	}
	/**
	 * Gets the name of the callback function to use when _JSONP is true.
	 * Defaults to "callback".
	 * @return string the name of the callback
	 */
	public function getCallback() {
		return $this->_callback;
	}
	
	/**
	 * Sets the name of the callback function to use when _JSONP is true.
	 * Defaults to "callback".
	 * @param string $value the name of the callback
	 */
	public function setCallback($value) {
		return $this->_callback = $value;
	}
	
	
	/**
	 * Renders the appropriate JSON output
	 * @param boolean $sendHeaders Whether to send headers or not, defaults to true.
	 * @param boolean $return Whether to return the output, defaults to false meaning the rendered content will be echoed
	 * @return string the rendering result if return is set to true, otherwise null
	 */
	public function render($sendHeaders = true, $return = false) {
		if ($sendHeaders) {
			$this->sendHeaders();
		}
		$result = $this->toArray();
		if (function_exists("json_encode")) {
			$result = json_encode($result);
		}
		else {
			$result = CJSON::encode($result);
		}
		if ($return) {
			return $result;
		}
		echo $result;
	}
	
	/**
	 * Sends the JSON headers
	 */
	public function sendHeaders() {
		header('Content-type: application/json');
		// turn off any log routes that might affect the output
		foreach(Yii::app()->log->routes as $logRoute) {
			if ($logRoute instanceof CProfileLogRoute || $logRoute instanceof CWebLogRoute) {
				$logRoute->enabled = false;
			}
		}
	}
}
