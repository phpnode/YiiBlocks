<?php
include("common.php"); // include the functionality common to all solr tests
/**
 * Tests for the {@link ASolrDocument} class
 * @author Charles Pick/PeoplePerHour.com
 * @package packages.solr.tests
 */
class ASolrDocumentTest extends CTestCase {
	/**
	 * Tests the magic getters and setters
	 */
	public function testMagicMethods() {
		$doc = new ASolrDocument();
		$this->assertFalse(isset($doc->name));
		$doc->name = "test name";
		$this->assertTrue(isset($doc->name));
		$this->assertEquals("test name", $doc->name);
	}
	/**
	 * Tests the attributeNames() method
	 */
	public function testAttributeNames() {
		$doc = new ASolrDocument();
		$doc->name = "test item";
		$this->assertEquals(array("name"),$doc->attributeNames());
		$doc = new ExampleExtendedSolrDocument();
		$this->assertEquals(array("incubationdate_dt"),$doc->attributeNames());
		$doc->name = "test item";
		$this->assertEquals(array("name","incubationdate_dt"),$doc->attributeNames());
	}
	/**
	 * Tests the primary key methods
	 */
	public function testPrimaryKey() {
		$doc = new ASolrDocument();
		$doc->name = "test";
		$doc->id = 12;
		$this->assertEquals("id", $doc->primaryKey());
		$this->assertEquals(12, $doc->getPrimaryKey());
	}
	/**
	 * Tests the save method
	 */
	public function testSave() {
		foreach($this->fixtureData() as $attributes) {
			$doc = new ASolrDocument();
			$doc->setAttributes($attributes); // should fail because of massive assignment on unsafe attributes
			$this->assertEquals(array(),$doc->getAttributes());
			$doc->setAttributes($attributes,false);
			$this->assertEquals(3,count($doc->getAttributes()));
			$this->assertTrue($doc->save());
		}
	}

	/**
	 * Generates 50 arrays of attributes for fixtures
	 * @return array the fixture data
	 */
	protected function fixtureData() {
		$rows = array();
		for($i = 0; $i < 50; $i++) {
			$rows[] = array(
				"id" => 400 + $i,
				"name" => "Test Item ".$i,
				"popularity" => $i
			);
		}
		return $rows;
	}
}
/**
 * An example of an extended solr document
 * @author Charles Pick/PeoplePerHour.com
 * @package packages.solr.tests
 */
class ExampleExtendedSolrDocument extends ASolrDocument {
	/**
	 * An example of a class property that will be saved to solr
	 * @var string
	 */
	public $incubationdate_dt;
}