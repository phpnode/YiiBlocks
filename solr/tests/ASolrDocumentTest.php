<?php
include_once("common.php"); // include the functionality common to all solr tests
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
		$connection = new ASolrConnection();
		$connection->clientOptions->hostname = SOLR_HOSTNAME;
		$connection->clientOptions->port = SOLR_PORT;
		$connection->clientOptions->path = SOLR_PATH;
		ASolrDocument::$solr = $connection;
		foreach($this->fixtureData() as $attributes) {

			$doc = new ASolrDocument();
			$doc->setAttributes($attributes); // should fail because of massive assignment on unsafe attributes
			$this->assertEquals(array(),$doc->getAttributes());
			$doc->setAttributes($attributes,false);
			$this->assertEquals(3,count($doc->getAttributes()));
			$this->assertTrue($doc->save());
		}
		$connection->commit();
	}
	/**
	 * Tests the named scope features
	 */
	public function testNamedScopes() {
		$model = ExampleExtendedSolrDocument::model();
		$model->exampleScope(); // apply the scope
		$models = $model->findAll();
		$this->assertGreaterThan(49, count($models));
	}
	/**
	 * Tests the find methods
	 */
	public function testFind() {
		$connection = new ASolrConnection();
		$connection->clientOptions->hostname = SOLR_HOSTNAME;
		$connection->clientOptions->port = SOLR_PORT;
		$connection->clientOptions->path = SOLR_PATH;
		ASolrDocument::$solr = $connection;
		$pkList = array();
		foreach($this->fixtureData() as $attributes) {
			$criteria = new ASolrCriteria;
			$criteria->query = "id:".$attributes['id'];
			$doc = ASolrDocument::model()->find($criteria);
			$this->assertTrue(is_object($doc));
			$this->assertTrue($doc instanceof ASolrDocument);
			foreach($attributes as $attribute => $value) {
				$this->assertEquals($value,$doc->{$attribute});
			}
			$pkList[$doc->getPrimaryKey()] = $attributes;
		}
		$criteria = new ASolrCriteria();
		$criteria->limit = 100;
		$criteria->addInCondition("id",array_keys($pkList));
		$models = ASolrDocument::model()->findAll($criteria);
		$this->assertEquals(count($pkList),count($models));
		foreach($models as $doc) {
			foreach($pkList[$doc->getPrimaryKey()] as $attribute => $value) {
				$this->assertEquals($value,$doc->{$attribute});
			}
		}
	}

	/**
	 * Tests the find by pk methods
	 */
	public function testFindByPk() {
		$connection = new ASolrConnection();
		$connection->clientOptions->hostname = SOLR_HOSTNAME;
		$connection->clientOptions->port = SOLR_PORT;
		$connection->clientOptions->path = SOLR_PATH;
		ASolrDocument::$solr = $connection;
		$pkList = array();
		foreach($this->fixtureData() as $attributes) {
			$doc = ASolrDocument::model()->findByPk($attributes['id']);
			$this->assertTrue(is_object($doc));
			$this->assertTrue($doc instanceof ASolrDocument);
			foreach($attributes as $attribute => $value) {
				$this->assertEquals($value,$doc->{$attribute});
			}
			$pkList[$doc->getPrimaryKey()] = $attributes;
		}
		$criteria = new ASolrCriteria();
		$criteria->limit = 100;
		$models = ASolrDocument::model()->findAllByPk(array_keys($pkList),$criteria);
		$this->assertEquals(count($pkList),count($models));
		foreach($models as $doc) {
			foreach($pkList[$doc->getPrimaryKey()] as $attribute => $value) {
				$this->assertEquals($value,$doc->{$attribute});
			}
		}
	}

	/**
	 * Tests the find by attributes methods
	 */
	public function testFindByAttributes() {
		$connection = new ASolrConnection();
		$connection->clientOptions->hostname = SOLR_HOSTNAME;
		$connection->clientOptions->port = SOLR_HOSTNAME;
		$connection->clientOptions->path = SOLR_PATH;
		ASolrDocument::$solr = $connection;
		foreach($this->fixtureData() as $attributes) {
			$doc = ASolrDocument::model()->findByAttributes($attributes);
			$this->assertTrue(is_object($doc));
			$this->assertTrue($doc instanceof ASolrDocument);
			foreach($attributes as $attribute => $value) {
				$this->assertEquals($value,$doc->{$attribute});
			}
		}
		foreach($this->fixtureData() as $attributes) {
			$models = ASolrDocument::model()->findAllByAttributes($attributes);
			$this->assertEquals(1,count($models));
			$doc = array_shift($models);
			$this->assertTrue(is_object($doc));
			$this->assertTrue($doc instanceof ASolrDocument);
			foreach($attributes as $attribute => $value) {
				$this->assertEquals($value,$doc->{$attribute});
			}
		}
	}
	/**
	 * Tests the delete method
	 */
	public function testDelete() {
		$connection = new ASolrConnection();
		$connection->clientOptions->hostname = SOLR_HOSTNAME;
		$connection->clientOptions->port = SOLR_PORT;
		$connection->clientOptions->path = SOLR_PATH;
		ASolrDocument::$solr = $connection;
		foreach($this->fixtureData() as $attributes) {
			$doc = ASolrDocument::model()->findByPk($attributes['id']);
			$this->assertTrue(is_object($doc));
			$this->assertTrue($doc->delete());
		}
		$connection->commit();
		// now check if they were really deleted
		foreach($this->fixtureData() as $attributes) {
			$doc = ASolrDocument::model()->findByPk($attributes['id']);
			$this->assertFalse(is_object($doc));
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

	/**
	 * An example of a solr named scope
	 * @return ExampleExtendedSolrDocument $this with the scope applied
	 */
	public function exampleScope() {
		$criteria = new ASolrCriteria();
		$criteria->setLimit(100);
		$criteria->setQuery('name:test');
		$this->getSolrCriteria()->mergeWith($criteria);
		return $this;
	}
	/**
	 * Gets the static model
	 * @param string $className the model class to instantiate
	 * @return ExampleExtendedSolrDocument the nidek
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
}