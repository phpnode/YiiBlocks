<?php
/**
 * A service response represents information about a response to a client request.
 * By implementing the appropriate child classes, a service response can be in any format such as JSON, XML, JSONP etc
 * @author Charles Pick
 * @package packages.services
 */
abstract class AServiceResponse extends CAttributeCollection {
	/**
	 * @var boolean whether the keys are case-sensitive. Defaults to true.
	 */
	public $caseSensitive=true;
	/**
	 * Renders the service response, including sending headers if appropriate
	 * @param boolean $sendHeaders Whether to send headers or not, defaults to true.
	 * @param boolean $return Whether to return the output, defaults to false meaning the rendered content will be echoed
	 */	
	abstract public function render($sendHeaders = true, $return = false);
	
	
	/**
	 * Sends the headers for this response type.
	 * Child classes should override this, the default implementation does nothing.
	 */
	public function sendHeaders() {
		// do nothing
	}
	
}
