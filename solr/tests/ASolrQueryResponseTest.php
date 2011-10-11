<?php
include_once("common.php"); // include the functionality common to all solr tests
/**
 * Tests for the {@link ASolrQueryResponse} class
 * @author Charles Pick/PeoplePerHour.com
 * @package packages.solr.tests
 */
class ASolrQueryResponseTest extends CTestCase {

	/**
	 * Tests the facet functions
	 */
	public function testFacets() {
		$queryResponse = new ASolrQueryResponse($this->mockSolrObject(), $this->mockSolrCriteria());
		$facets = $queryResponse->getFieldFacets();
		$this->assertEquals(1, count($facets));
		$this->assertTrue(isset($facets->name));
		$this->assertTrue($facets->name instanceof ASolrFacet);

	}
	/**
	 * Tests the results functions
	 */
	public function testResults() {
		$queryResponse = new ASolrQueryResponse($this->mockSolrObject(), $this->mockSolrCriteria());
		$results = $queryResponse->getResults();
		$this->assertEquals(3,$results->count());
		foreach($results as $n => $result) {
			$this->assertEquals("test item ".($n + 1),$result->name);
		}
	}
	/**
	 * Creates a mock solr criteria for use in testing
	 * @return ASolrCriteria the mock criteria
	 */
	protected function mockSolrCriteria() {
		$criteria = new ASolrCriteria;
		$criteria->query = "test item";
		$criteria->offset = 0;
		$criteria->limit = 10;
		return $criteria;
	}
	/**
	 * Creates a mock solr object for use in testing
	 * @return object the mock solr object
	 */
	protected function mockSolrObject() {
		$mock = (object) array(
			"responseHeader" => (object) array(),
			"response" => (object) array(
				"numFound" => 2,
				"start" => 0,
				"docs" => array(
					(object) array(
						"id" => 1,
						"name" => "test item 1",
						"popularity" => 1,
					),
					(object) array(
						"id" => 2,
						"name" => "test item 2",
						"popularity" => 2,
					),
					(object) array(
						"id" => 3,
						"name" => "test item 3",
						"popularity" => 3,
					)
				)
			),
			"facet_counts" => (object) array(
				"facet_queries" => (object) array(),
				"facet_fields" => (object) array(
					"name" => (object) array(
						"test item 1" => 1,
						"test item 2" => 1,
						"test item 3" => 1,
					)
				),
			),
		);
		return $mock;
	}

}