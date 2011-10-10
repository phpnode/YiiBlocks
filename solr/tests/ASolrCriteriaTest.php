<?php
include("common.php"); // include the functionality common to all solr tests
/**
 * Tests for the {@link ASolrCriteria} class
 * @author Charles Pick/PeoplePerHour.com
 * @package packages.solr.tests
 */
class ASolrCriteriaTest extends CTestCase {

	/**
	 * tests the magic getters and setters
	 */
	public function testGettersAndSetters() {
		$criteria = new ASolrCriteria();
		$this->assertTrue(isset($criteria->fields));
		$this->assertTrue(isset($criteria->filterQueries));
	}
	/**
	 * Tests the limit and offset magic getters / setters that are required for compatibility with pagination
	 */
	public function testLimitAndOffset() {
		$criteria = new ASolrCriteria();
		$this->assertEquals(null,$criteria->limit);
		$criteria->limit = 100;
		$this->assertEquals(100,$criteria->getRows());
		$this->assertEquals(null, $criteria->offset);
		$criteria->offset = 20;
		$this->assertEquals(20,$criteria->getStart());
	}
}