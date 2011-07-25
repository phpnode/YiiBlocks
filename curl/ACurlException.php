<?php
/**
 * Wraps curl errors
 * @author Charles Pick
 * @package packages.curl
 */
class ACurlException extends CException {
	/**
	 * Holds the response
	 * @var ACurlResponse
	 */
	public $response;
	
	/**
	 * @var integer HTTP status code, such as 403, 404, 500, etc.
	 */
	public $statusCode;
	
	/**
	 * Constructor.
	 * @param integer $status CURL status code, such as 404, 500, etc.
	 * @param string $message error message
	 * @param ACurlResponse $response The CURL response
	 * @param integer $code error code
	 */
	public function __construct($status,$message=null,$response = null, $code=0)
	{
		$this->statusCode=$status;
		$this->response = $response;
		$message .= ":\n".$response->data;
		parent::__construct($message,$code);
	}
	 
}