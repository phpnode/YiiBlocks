<?php
include_once("common.php");
/**
 * Tests for the {@link ASolrDataProvider} class
 * @author Charles Pick / PeoplePerHour.com
 * @package packages.solr.tests
 */
class ASolrDataProviderTest extends CTestCase {
	/**
	 * Sets up the environment for this test
	 */
	public function setUp() {
		$this->getConnection();
		foreach($this->fixtureData() as $attributes) {
			$doc = new ASolrDocument();
			$doc->setAttributes($attributes,false);
			$this->assertTrue($doc->save());
		}
		$this->getConnection()->commit();
	}
	/**
	 * Tests the basic functionality
	 */
	public function testBasics() {
		$dataProvider = new ASolrDataProvider("ASolrDocument");
		$dataProvider->getCriteria()->query = "name:test";
		$data = $dataProvider->getData();
		$this->assertEquals(10,count($data));
		$this->assertEquals(55,$dataProvider->getTotalItemCount());
		$dataProvider->getPagination()->setCurrentPage(5);
		$data = $dataProvider->getData(true);
		$this->assertEquals(5,count($data));
	}
	/**
	 * Tests the facet functions
	 */
	public function testFacets() {
		$dataProvider = new ASolrDataProvider("ASolrDocument");
		$criteria = $dataProvider->getCriteria();
		$criteria->setQuery("name:test");
		$criteria->facet = true;
		$criteria->addFacetField("name");
		$criteria->addFacetQuery("popularity:[* TO 10]");
		$criteria->addFacetQuery("popularity:[10 TO 20]");
		$criteria->addFacetQuery("popularity:[20 TO *]");
		$this->assertEquals(55, $dataProvider->getTotalItemCount());
		$fieldFacets = $dataProvider->getFieldFacets();
		$this->assertTrue(isset($fieldFacets->name));
		$this->assertTrue($fieldFacets->name instanceof ASolrFacet);
		$this->assertTrue(isset($fieldFacets->name['test']));
		$this->assertEquals(55, $fieldFacets->name['test']);

		$queryFacets = $dataProvider->getQueryFacets();
		$this->assertTrue(isset($queryFacets["popularity:[* TO 10]"]));
		$this->assertEquals(11, $queryFacets["popularity:[* TO 10]"]['value']);
	}

	/**
	 * Destroys the test data at the end of the test
	 */
	public function tearDown() {
		foreach($this->fixtureData() as $attributes) {
			$doc = ASolrDocument::model()->findByAttributes($attributes);
			$this->assertTrue($doc->delete());
		}
		$this->getConnection()->commit();
	}

	/**
	 * Gets the solr connection
	 * @return ASolrConnection the connection to use for this test
	 */
	protected function getConnection() {
		static $connection;
		if ($connection === null) {
			$connection = new ASolrConnection();
			$connection->clientOptions->hostname = SOLR_HOSTNAME;
			$connection->clientOptions->port = SOLR_PORT;
			$connection->clientOptions->path = SOLR_PATH;
			ASolrDocument::$solr = $connection;
		}
		return $connection;
	}
	/**
	 * Generates 55 arrays of attributes for fixtures
	 * @return array the fixture data
	 */
	protected function fixtureData() {
		$rows = array();
		for($i = 0; $i < 55; $i++) {
			$rows[] = array(
				"id" => 400 + $i,
				"name" => "Test Item ".$i,
				"popularity" => $i
			);
		}
		return $rows;
	}
}