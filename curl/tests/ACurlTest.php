<?php
/**
 * A series of tests for the ACurl class.
 * @see ACurl
 * @package application.tests.unit
 * @author Charles Pick
 */
class ACurlTest extends CTestCase {
	/**
	 * Tests the curl options
	 */
	public function testOptions() {
		$curl = new ACurl;
		$this->assertTrue($curl->options instanceof ACurlOptions);

		// test default options

		$this->assertEquals("Yii PHP Framework / ACurl",$curl->options->userAgent);
		$this->assertEquals(true, $curl->options->header);
		$this->assertEquals(true, $curl->options->followLocation);
		$this->assertEquals(true, $curl->options->returnTransfer);
		$this->assertEquals(30, $curl->options->timeout);
		$this->assertEquals("gzip", $curl->options->encoding);
		$this->assertEquals(false, $curl->options->ssl_verifypeer);

		// test setting options

		$userAgent = "Fake User Agent";
		$curl->options->userAgent = $userAgent;

		$this->assertEquals("CURLOPT_USERAGENT",$curl->options->getConstantName("userAgent"));

	}
	/**
	 * Tests the header parsing
	 */
	public function testHeaders() {
		$curl = new ACurl;
		$response = $curl->head("http://facebook.com/")->exec();
		$this->assertEquals(2,$response->headers->count); // facebook.com always redirects to www.facebook.com
		$this->assertEquals(301, $response->headers[0]->http_code);
		$this->assertEquals(200, $response->headers[1]->http_code);
		$this->assertEquals("no-cache",$response->headers[1]->pragma);
	}

	/**
	 * Tests the response to string methods
	 */
	public function testToString() {
		$curl = new ACurl;
		$this->assertTrue($curl->get("http://www.php.net/") instanceof ACurl);
		$response = $curl->exec();
		$this->assertTrue($response instanceof ACurlResponse);
		$this->assertTrue((bool) strstr($response,"downloads"));
	}

	/**
	 * Tests getting data from a non existant domain.
	 * @expectedException ACurlException
	 */
	public function testGetNoSuchDomain() {
		$curl = new ACurl;
		$this->assertTrue($curl->get("http://404.php.net/") instanceof ACurl);
		$response = $curl->exec();
	}

	/**
	 * Tests getting data from a non existant URL.
	 * @expectedException ACurlException
	 */
	public function testGet404() {
		$curl = new ACurl;
		$this->assertTrue($curl->get("http://www.google.com/arglebarcle") instanceof ACurl);
		$response = $curl->exec();

	}

	/**
	 * Tests getting a (valid) URL.
	 */
	public function testGet() {
		$curl = new ACurl;
		$this->assertTrue($curl->get("http://www.php.net/") instanceof ACurl);
		$response = $curl->exec();
		$this->assertTrue($response instanceof ACurlResponse);
		$this->assertTrue((bool) strstr($response->data,"downloads"));
	}
	/**
	 * Tests posting data to a non existant domain.
	 * @expectedException ACurlException
	 */
	public function testPostNonExistant() {
		$curl = new ACurl;
		$this->assertTrue($curl->post("http://404.php.net/",array("test" => "test")) instanceof ACurl);
		$response = $curl->exec();
	}

	/**
	 * Tests posting data to a 404 url
	 * @expectedException ACurlException
	 */
	public function testPost404() {
		$curl = new ACurl;
		$this->assertTrue($curl->post("http://www.google.com/404nosuchPAGE",array("test" => "test")) instanceof ACurl);
		$response = $curl->exec();
	}
	/**
	 * Tests the caching features.
	 * Warning, assumes our cache is at least 10x as fast as google
	 */
	public function testCache() {
		$curl = new ACurl;
		$startTime = microtime(true);
		$curl->cache(10)->get("http://www.google.com/")->exec();

		$endTime = microtime(true);
		$fetchTime = $endTime - $startTime;
		//echo "Fetched in ".$fetchTime." seconds\n";
		// make 1000 requests to google, they should all be cached
		$startTime = microtime(true);
		for ($i = 0; $i <= 1000; $i++) {
			$curl = new ACurl;

			$curl->cache(10)->get("http://www.google.com/")->exec();

		}
		$endTime = microtime(true);

		$totalTime = $endTime - $startTime;
		//echo "1000 Fetched in ".$totalTime." seconds\n";
		$this->assertTrue($totalTime / 100 < $fetchTime);
	}
	/**
	 * Tests performing a http head request for a valid URL
	 */
	public function testHead() {
		$curl = new ACurl;
		$curl->head("http://www.php.net/");
		$response = $curl->exec()->info;
		$this->assertEquals("http://www.php.net/",$response->url);
		$this->assertEquals("text/html;charset=utf-8",$response->content_type);
		$this->assertEquals(200,$response->http_code);

	}

	/**
	 * Tests performing a http head request for a non existant URL
	 * @expectedException ACurlException
	 */
	public function testHead404() {
		$curl = new ACurl;
		$curl->head("http://uk3.php.net/manual/en/arglebargle");
		$response = $curl->exec()->info;
		$this->assertEquals("text/html;charset=utf-8",$response->content_type);
		$this->assertEquals(404,$response->http_code);

	}

}
