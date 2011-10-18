<?php
include_once("common.php"); // include the functionality common to all solr tests
/**
 * Tests for the {@link ASolrConnection} class
 * @author Charles Pick/PeoplePerHour.com
 * @package packages.solr.tests
 */
class ASolrConnectionTest extends CTestCase {
	/**
	 * Tests the client options
	 * @expectedException Exception
	 */
	public function testClientOptions() {
		$connection = new ASolrConnection();
		$connection->clientOptions->hostname = SOLR_HOSTNAME;
		$connection->clientOptions->port = SOLR_PORT;
		$connection->clientOptions->path = SOLR_PATH;
		$client = $connection->getClient();
		$this->assertTrue($client instanceof SolrClient);
		$connection->clientOptions = array();
		$connection->setClient(null);
		$connection->getClient(); // should throw an exception
	}
	/**
	 * Tests adding and removing a document from solr
	 */
	public function testIndexAndDelete() {
		$connection = new ASolrConnection();
		$connection->clientOptions->hostname = SOLR_HOSTNAME;
		$connection->clientOptions->port = SOLR_PORT;
		$connection->clientOptions->path = SOLR_PATH;
		$doc = new SolrInputDocument();

		$doc->addField('id', 334455);
		$doc->addField('cat', 'Software');
		$doc->addField('cat', 'Lucene');
		$this->assertTrue($connection->index($doc));
		$this->assertTrue($connection->delete(334455));

		// now test bulk actions
		$docList = array();
		for($i = 0; $i < 10; $i++) {
			$item = clone $doc;
			$item->getField("id")->values[0] =  999999 + $i;
			$docList[] = $item;
		}
		$this->assertTrue($connection->index($docList));
		$this->assertTrue($connection->delete(array_map(
			function($a) {
				return $a->getField("id")->values[0];
			},
			$docList)));
		$this->assertTrue($connection->commit());
	}
	/**
	 * Tests the faceted search functions
	 */
	public function testFacetedSearch() {
		$connection = new ASolrConnection();
		$connection->clientOptions->hostname = SOLR_HOSTNAME;
		$connection->clientOptions->port = SOLR_PORT;
		$connection->clientOptions->path = SOLR_PATH;
		$doc = new SolrInputDocument();

		$doc->addField('id', 334455);
		$doc->addField('cat', 'Software');
		$doc->addField('cat', 'Lucene');
		$doc->addField("popularity",20);
		$doc->addField("incubationdate_dt",date("Y-m-d\TH:i:s\Z"));
		$this->assertTrue($connection->index($doc));
		$this->assertTrue($connection->commit());
		$criteria = new ASolrCriteria;
		$criteria->query = "lucene";
		$criteria->offset = 0;
		$criteria->limit = 10;
		$criteria->facet = true;
		$criteria->addFacetField("cat")->addFacetField("name");
		$criteria->addFacetDateField("incubationdate_dt");
		$criteria->facetDateStart = "2005-10-10T00:00:00Z";
		$criteria->facetDateEnd = "2015-10-10T00:00:00Z";
		$criteria->facetDateGap = "+12MONTH";
		$criteria->addFacetQuery("popularity:[* TO 10]");
		$criteria->addFacetQuery("popularity:[10 TO 20]");
		$criteria->addFacetQuery("popularity:[20 TO *]");

		$response = $connection->search($criteria);
		$this->assertTrue($response instanceof ASolrQueryResponse);
		$this->assertTrue(isset($response->getDateFacets()->incubationdate_dt));
		$this->assertTrue($response->getDateFacets()->incubationdate_dt instanceof ASolrFacet);

		$results = $response->getResults();
		$this->assertTrue(count($results) > 0);
		foreach($results as $n => $result) {
			$this->assertEquals($n, $result->getPosition());
		}
		$this->assertTrue($connection->delete(334455));
		$this->assertTrue($connection->commit());
	}

}