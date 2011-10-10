<?php
/**
 * Tests for the XML response class
 * @author Charles Pick
 * @package packages.services.tests
 */
class AXMLResponseTest extends CTestCase {
	/**
	 * Tests the buildXML() function
	 */
	public function testBuildXML() {
		$sampleData = array(
			"name" => "Charles",
			"address" => "123 Fake Street",
			"messages" => array(
					array(
						"subject" => "This is a test 1",
						"content" => "This is a test message 1",
					),
					array(
						"subject" => "This is a test 2",
						"content" => "This is a test message 2",
					),
			)
		);
		$response = new AXMLResponse($sampleData);
		$xml = $response->render(false,true);
		$this->assertTrue(strpos($xml,"<document>") > -1);

	}


}