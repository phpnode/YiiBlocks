<?php

/**
 * Akismet anti-comment spam service
 *
 * The class in this package allows use of the {@link http://akismet.com Akismet} anti-comment spam service in any Yii application.
 *
 * This service performs a number of checks on submitted data and returns whether or not the data is likely to be spam.
 *
 * Please note that in order to use this class, you must have a vaild {@link https://akismet.com/signup/ Akismet API key}.  They are free for non/small-profit types and getting one will only take a couple of minutes.  
 *
 * For commercial use, please {@link http://akismet.com/commercial/ visit the Akismet commercial licensing page}.
 * The Akismet Application component, provides blog comment spam filtering. 
 * 
 * Based heavily on the code by Alex Potsides ({@link http://www.achingbrain.net}).
 * This class takes the functionality from the Akismet WordPress plugin written by {@link http://photomatt.net/ Matt Mullenweg} and allows it to be integrated into any Yii application
 *
 * The original plugin is {@link http://akismet.com/download/ available on the Akismet website}.
 *
 * <b>Configuration:</b><br />
 * AAkismet is an application component and should be configured in your main config
 * <pre>
 * 'components' => array(
 *		'akismet' => array(
 * 			'class' => "AAkismet",
 * 			'apiKey' => 'Your Api Key'.
 *		),
 *  	...
 * )
 * </pre>
 * <b>Usage:</b>
 * <pre>
 * $akismet = Yii::app()->akismet;
 * $akismet->commentAuthor = $name;
 * $akismet->commentAuthorEmail = $email;
 * $akismet->commentAuthorURL = $url;
 * $akismet->commentContent = $comment;
 * $akismet->permalink = 'http://www.example.com/blog/alex/someurl/';
 * if($akismet->isCommentSpam())
 *   // store the comment but mark it as spam (in case of a mis-diagnosis)
 * else
 *   // store the comment normally
 * </pre>
 *
 * Optionally you may wish to check if your Akismet API key is valid as in the example below.
 * 
 * <pre>
 * $akismet = Yii::app()->akismet;
 * if($akismet->isKeyValid()) {
 * 	// api key is okay
 * } else {
 * 	// api key is invalid
 * }
 * </pre>
 *
 * @package	akismet
 * @version	1
 * @author		Alex Potsides, Charles Pick
 * @link		http://www.achingbrain.net/
 * @copyright	Alex Potsides, {@link http://www.achingbrain.net http://www.achingbrain.net}
 * @license		http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class AAkismet extends CApplicationComponent {
	/**
	 * The akismet API key. This is required for all interaction with Akismet
	 * @var string
	 */
	public $apiKey;
	
	private $_version = '0.1';
	private $_comment = array();
	private $_apiPort;
	private $_akismetServer;
	private $_akismetVersion;
	
	// This prevents some potentially sensitive information from being sent accross the wire.
	private $_ignore = array('HTTP_COOKIE', 
							'HTTP_X_FORWARDED_FOR', 
							'HTTP_X_FORWARDED_HOST', 
							'HTTP_MAX_FORWARDS', 
							'HTTP_X_FORWARDED_SERVER', 
							'REDIRECT_STATUS', 
							'SERVER_PORT', 
							'PATH',
							'DOCUMENT_ROOT',
							'SERVER_ADMIN',
							'QUERY_STRING',
							'PHP_SELF' );
	
	/**
	 * Initializes the Akismet application component
	 */
	public function init() {
		
		
		// Start to populate the comment data
		$this->getBlogURL();
		$this->getUserAgent();
		$this->getReferrer();
		$this->getUserIP();
		$this->getUserAgent();		
		
		
	}
	
	/**
	 * Makes a request to the Akismet service to see if the API key passed to the constructor is valid.
	 * 
	 * Use this method if you suspect your API key is invalid.
	 * 
	 * @return bool	True is if the key is valid, false if not.
	 */
	public function isKeyValid() {
		// Check to see if the key is valid
		$response = $this->sendRequest('key=' . $this->apiKey . '&blog=' . $this->getBlogURL(), $this->getAkismetServer(), '/' . $this->getAkismetVersion() . '/verify-key');
		return $response[1] == 'valid';
	}
	
	/**
	 * makes a request to the Akismet service
	 * @param string $request The resquest to send
	 * @param string $host The akismet server hostname
	 * @param string $path The path to the web service
	 * @return array The lines of the request
	 */
	private function sendRequest($request, $host, $path) {
		if ($this->apiKey === null) {
			throw new CException("No Akismet API Key specified");
		}
		$http_request  = "POST " . $path . " HTTP/1.0\r\n";
		$http_request .= "Host: " . $host . "\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";
		$http_request .= "Content-Length: " . strlen($request) . "\r\n";
		$http_request .= "User-Agent: Akismet Yii Component " . $this->_version . " | Akismet/1.11\r\n";
		$http_request .= "\r\n";
		$http_request .= $request;
		
		$socketWriteRead = new AAkismetSocketWriteRead($host, $this->getApiPort(), $http_request);
		$socketWriteRead->send();
		
		return explode("\r\n\r\n", $socketWriteRead->getResponse(), 2);
	}
	
	/**
	 * Formats the data for transmission
	 * @return string the formatted data
	 */
	private function getQueryString() {
		foreach($_SERVER as $key => $value) {
			if(!in_array($key, $this->_ignore)) {
				if($key == 'REMOTE_ADDR') {
					$this->_comment[$key] = $this->_comment['user_ip'];
				} else {
					$this->_comment[$key] = $value;
				}
			}
		}

		$query_string = '';
		
		foreach($this->_comment as $key => $data) {
			if(!is_array($data)) {
				$query_string .= $key . '=' . urlencode(stripslashes($data)) . '&';
			}
		}
		
		return $query_string;
	}
	
	/**
	 *	Tests for spam.
	 *
	 *	Uses the web service provided by {@link http://www.akismet.com Akismet} to see whether or not the submitted comment is spam.  Returns a boolean value.
	 *
	 *	@return	bool True if the comment is spam, false if not
	 *  @throws	Will throw an exception if the API key passed to the constructor is invalid.
	 */
	public function isCommentSpam() {
		$response = $this->sendRequest($this->getQueryString(), $this->apiKey . '.rest.akismet.com', '/' . $this->getAkismetVersion() . '/comment-check');
		
		if($response[1] == 'invalid' && !$this->isKeyValid()) {
			throw new exception('The Akismet API key passed to the Akismet constructor is invalid.  Please obtain a valid one from https://akismet.com/signup/');
		}
		
		return ($response[1] == 'true');
	}

	/**
	 *	Submit spam that is incorrectly tagged as ham.
	 *
	 *	Using this function will make you a good citizen as it helps Akismet to learn from its mistakes.  This will improve the service for everybody.
	 */
	public function submitSpam() {
		$this->sendRequest($this->getQueryString(), $this->apiKey . '.' . $this->getAkismetServer(), '/' . $this->getAkismetVersion() . '/submit-spam');
	}
	
	/**
	 *	Submit ham that is incorrectly tagged as spam.
	 *
	 *	Using this function will make you a good citizen as it helps Akismet to learn from its mistakes.  This will improve the service for everybody.
	 */
	public function submitHam() {
		$this->sendRequest($this->getQueryString(), $this->apiKey . '.' . $this->getAkismetServer(), '/' . $this->getAkismetVersion() . '/submit-ham');
	}
	
	/**
	 *	To override the user IP address when submitting spam/ham later on
	 *
	 *	@param string $userip	An IP address.  Optional.
	 */
	public function setUserIP($userip) {
		$this->_comment['user_ip'] = $userip;
	}
	
	/**
	 * Gets the IP of the user who submitted this comment
	 * @return string An IP address
	 */
	public function getUserIP() {
		if (!isset($this->_comment['user_ip']) && isset($_SERVER['REMOTE_ADDR'])) {
			/* 
			 * This is necessary if the server PHP5 is running on has been set up to run PHP4 and
			 * PHP5 concurently and is actually running through a separate proxy al a these instructions:
			 * http://www.schlitt.info/applications/blog/archives/83_How_to_run_PHP4_and_PHP_5_parallel.html
			 * and http://wiki.coggeshall.org/37.html
			 * Otherwise the user_ip appears as the IP address of the PHP4 server passing the requests to the 
			 * PHP5 one...
			 */
			$this->_comment['user_ip'] = $_SERVER['REMOTE_ADDR'] != getenv('SERVER_ADDR') ? $_SERVER['REMOTE_ADDR'] : getenv('HTTP_X_FORWARDED_FOR');
		}
		return isset($this->_comment['user_ip']) ? $this->_comment['user_ip'] : null;
	}
	/**
	 * Sets the user agent to the given value
	 * @param string $value The user agent string
	 */
	public function setUserAgent($value) {
		$this->_comment['user_agent'] = $value;
	}
	/**
	 * Gets the user agent string
	 * @return string The user agent string
	 */
	public function getUserAgent() {
		if (!isset($this->_comment['user_agent']) && isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->_comment['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		}
		return isset($this->_comment['user_agent']) ? $this->_comment['user_agent'] : null;
	}
	/**
	 * Sets the referring page.
	 * Used to override the referring page when submitting spam/ham later on
	 * @param string $referrer The referring page.  Optional.
	 */
	public function setReferrer($referrer) {
		$this->_comment['referrer'] = $referrer;
	}
	
	/**
	 * Gets the referring page
	 * @return string The referring page
	 */
	public function getReferrer() {
		if (!isset($this->_comment['referrer']) && isset($_SERVER['HTTP_REFERER'])) {
			$this->_comment['referrer'] = $_SERVER['HTTP_REFERER'];
		}
		return isset($this->_comment['referrer']) ? $this->_comment['referrer'] : null;
	}
	
	
	/**
	 *	A permanent URL referencing the blog post the comment was submitted to.
	 *
	 *	@param string $permalink	The URL.  Optional.
	 */
	public function setPermalink($permalink) {
		$this->_comment['permalink'] = $permalink;
	}
	
		
	/**
	 * Gets a permanent URL referencing the blog post the comment was submitted to.
	 * @return string The URL
	 */
	public function getPermalink() {
		return isset($this->_comment['permalink']) ? $this->_comment['permalink'] : null;
	}
	
	
	/**
	 * Sets the type of comment being submitted.  
	 * @param string $commentType May be blank, comment, trackback, pingback, or a made up value like "registration" or "wiki".
	 */
	public function setCommentType($commentType) {
		$this->_comment['comment_type'] = $commentType;
	}
	
	/**
	 * Gets the type of comment being submitted.  
	 *
	 * @return string May be blank, comment, trackback, pingback, or a made up value like "registration" or "wiki".
	 */
	public function getCommentType() {
		return isset($this->_comment['comment_type']) ? $this->_comment['comment_type'] : null;
	}
	
	/**
	 * Sets the name that the author submitted with the comment.
	 * @param string $commentAuthor the name of the author
	 */
	public function setCommentAuthor($commentAuthor) {
		$this->_comment['comment_author'] = $commentAuthor;
	}
	
	/**
	 * Gets the name that the author submitted with the comment.
	 * @return string The name of the author
	 */
	public function getCommentAuthor() {
		return isset($this->_comment['comment_author']) ? $this->_comment['comment_author'] : null;
	}
	
	
	/**
	 * Sets the email address that the author submitted with the comment.
	 * 
	 * The address is assumed to be valid.
	 * @param string $authorEmail The author's email address
	 */
	public function setCommentAuthorEmail($authorEmail) {
		$this->_comment['comment_author_email'] = $authorEmail;
	}
	
	/**
	 * Gets the email address that the author submitted with the comment.
	 * 
	 * @return string The author's email address
	 */
	public function getCommentAuthorEmail() {
		return isset($this->_comment['comment_author_email']) ? $this->_comment['comment_author_email'] : null;
	}
	
	
	/**
	 * Sets the URL that the author submitted with the comment.
	 * @param string $authorURL The submitted URL
	 */	
	public function setCommentAuthorURL($authorURL) {
		$this->_comment['comment_author_url'] = $authorURL;
	}
	
	/**
	 * Gets the URL that the author submitted with the comment.
	 * @return string The submitted URL
	 */
	public function getCommentAuthorURL() {
		return isset($this->_comment['comment_author_url']) ? $this->_comment['comment_author_url'] : null;
	}
	/**
	 * Sets the comment's body text.
	 * @param string $commentBody The comment content
	 */
	public function setCommentContent($commentBody) {
		$this->_comment['comment_content'] = $commentBody;
	}
	/**
	 * Gets the comment's body text
	 * @return string the comment content
	 */
	public function getCommentContent() {
		return isset($this->_comment['comment_content']) ? $this->_comment['comment_content'] : null;
	}
	/**
	 * Sets the URL of the blog being checked
	 * @param string $url The url of the blog
	 */
	public function setBlogURL($url) {
		if (is_array($url)) {
			$url = Yii::app()->controller->createAbsoluteUrl(array_shift($url), $url);
		}
		$this->_comment['blog'] = $url;
	}
	/**
	 * Gets the blog URL to send to Akismet.
	 * Defaults to the application blog URL if possible, otherwise the application base URL.
	 * @return string The blog URL to send to Akismet
	 */
	public function getBlogURL() {
		if (!isset($this->_comment['blog'])) {
			if (is_object(Yii::app()->getModule("admin")) && isset(Yii::app()->getModule("admin")->getModule("blog")->blogRoute)) {
				$route = Yii::app()->getModule("admin")->getModule("blog")->blogRoute;
			}
			else {
				$route = array("/site/index");
			}
			if (is_array($route)) {
				$this->_comment['blog'] = Yii::app()->controller->createAbsoluteUrl(array_shift($route), $route);
			}
			else {
				$this->_comment['blog'] = $route;
			}
		}
		return $this->_comment['blog'];
	}
	
	/**
	 * Sets the API port
	 * @param integer $apiPort The Akismet API port
	 */
	public function setApiPort($apiPort) {
		$this->_apiPort = $apiPort;
	}
	
	/**
	 * Gets the API port, defaults to 80
	 * @return integer The Akismet API port
	 */
	public function getApiPort() {
		if ($this->_apiPort === null) {
			$this->_apiPort = 80;
		}
		return $this->_apiPort;
	}
	
	/**
	 * Sets the akismet server hostname
	 * @param string $akismetServer The server hostname
	 */
	public function setAkismetServer($akismetServer) {
		$this->_akismetServer = $akismetServer;
	}
	
	/**
	 * Gets the akismet server host name/
	 * Defaults to rest.akismet.com
	 * @return string the server hostname
	 */
	public function getAkismetServer() {
		if ($this->_akismetServer === null) {
			$this->_akismetServer = "rest.akismet.com";
		}
		return $this->_akismetServer;
	}
	
	
	/**
	 * Sets the Akismet API version
	 * @param string $akismetVersion The API version
	 */
	public function setAkismetVersion($akismetVersion) {
		$this->_akismetVersion = $akismetVersion;
	}
	
	/**
	 * Gets the Akismet API version
	 * Defaults to '1.1'
	 * @return string the server hostname
	 */
	public function getAkismetVersion() {
		if ($this->_akismetVersion === null) {
			$this->_akismetVersion = "1.1";
		}
		return $this->_akismetVersion;
	}
}

/**
 *	Utility class used by Akismet
 *
 *  This class is used by Akismet to do the actual sending and receiving of data.  It opens a connection to a remote host, sends some data and the reads the response and makes it available to the calling program.
 *
 *  The code that makes up this class originates in the Akismet WordPress plugin, which is {@link http://akismet.com/download/ available on the Akismet website}.
 *
 *	N.B. It is not necessary to call this class directly to use the Akismet class.  This is included here mainly out of a sense of completeness.
 *
 *	@package	akismet
 *	@name		AAkismetSocketWriteRead
 *	@version	0.1
 *  @author		Alex Potsides
 *  @link		http://www.achingbrain.net/
 */
class AAkismetSocketWriteRead {
	private $host;
	private $port;
	private $request;
	private $response;
	private $responseLength;
	private $errorNumber;
	private $errorString;
	
	/**
	 *	@param string $host The host to send/receive data.
	 *	@param int $port The port on the remote host.
	 *	@param string $request The data to send.
	 *	@param int $responseLength The amount of data to read.  Defaults to 1160 bytes.
	 */
	public function __construct($host, $port, $request, $responseLength = 1160) {
		$this->host = $host;
		$this->port = $port;
		$this->request = $request;
		$this->responseLength = $responseLength;
		$this->errorNumber = 0;
		$this->errorString = '';
	}
	
	/**
	 *  Sends the data to the remote host.
	 *
	 * @throws	An exception is thrown if a connection cannot be made to the remote host.
	 */
	public function send() {
		$this->response = '';
		
		$fs = fsockopen($this->host, $this->port, $this->errorNumber, $this->errorString, 3);
		
		if($this->errorNumber != 0) {
			throw new Exception('Error connecting to host: ' . $this->host . ' Error number: ' . $this->errorNumber . ' Error message: ' . $this->errorString);
		}
		
		if($fs !== false) {
			@fwrite($fs, $this->request);
			
			while(!feof($fs)) {
				$this->response .= fgets($fs, $this->responseLength);
			}
			
			fclose($fs);
		}
	}
	
	/**
	 *  Returns the server response text
	 *
	 *  @return	string
	 */
	public function getResponse() {
		return $this->response;
	}
	
	/**
	 *	Returns the error number
	 *
	 *	If there was no error, 0 will be returned.
	 *
	 *	@return int
	 */
	public function getErrorNumner() {
		return $this->errorNumber;
	}
	
	/**
	 *	Returns the error string
	 *
	 *	If there was no error, an empty string will be returned.
	 *
	 *	@return string
	 */
	public function getErrorString() {
		return $this->errorString;
	}
}

?>