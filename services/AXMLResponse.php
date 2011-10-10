<?php
/**
 * Holds information about an XML service response.
 * This is useful when responding to requests with XML, e.g.
 * <pre>
 * $response = new AXMLResponse();
 * $response->welcomeString = "Hello World";
 * $response->anotherField = "Something Else";
 * $response->render(); // sends the headers and renders the XML
 * </pre>
 *
 * @author Charles Pick
 * @package packages.services
 */
class AXMLResponse extends AServiceResponse {
	/**
	 * The name of the root document.
	 * Defaults to "document"
	 * @var string
	 */
	public $rootDocument = "document";

	/**
	 * Renders the appropriate JSON output
	 * @param boolean $sendHeaders Whether to send headers or not, defaults to true.
	 * @param boolean $return Whether to return the output, defaults to false meaning the rendered content will be echoed
	 * @return string|null the rendering result if return is set to true, otherwise null
	 */
	public function render($sendHeaders = true, $return = false) {
		if ($sendHeaders) {
			$this->sendHeaders();
		}
		$result = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$result .= $this->buildXML(array(
										$this->rootDocument => $this->toArray(),
								   ));
		if ($return) {
			return $result;
		}
		echo $result;
	}

	/**
	 * Builds XML from a given input array
	 * @param array $input The array to convert to XML
	 * @param integer $indent The amount to indent by
	 * @return string The XML
	 */
	public function buildXML(array $input, $indent = 0) {
		$output = "";
		foreach($input as $key => $value) {
			if (is_numeric($key)) {
				$key = "item";
			}
			$output .= str_repeat("\t", $indent)."<".$key.">\n";
			if (is_array($value)) {
				$output .= $this->buildXML($value,$indent + 1);
			}
			else {
				static $escapeCharacters = array(
					"<",">","&",
				);

				$encodable = false;
				if (!strstr($value,"<![CDATA[")) {
					foreach($escapeCharacters as $character) {
						if (strstr($value,$character)) {
							$encodable = true;
							break;
						}
					}
				}
				if ($encodable) {
					$output .= str_repeat("\t",$indent + 1)."<![CDATA[".$value."]]>\n";
				}
				else {
					$output .= str_repeat("\t",$indent + 1).$value."\n";
				}
			}
			$output .= str_repeat("\t", $indent)."</".$key.">\n";

		}
		return $output;
	}

	/**
	 * Sends the JSON headers
	 */
	public function sendHeaders() {
		header('Content-type: text/xml');

		// turn off any log routes that might affect the output
		foreach(Yii::app()->log->routes as $logRoute) {
			if ($logRoute instanceof CProfileLogRoute || $logRoute instanceof CWebLogRoute) {
				$logRoute->enabled = false;
			}
		}
	}
}
