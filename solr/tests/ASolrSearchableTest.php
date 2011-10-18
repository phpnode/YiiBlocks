<?php
include_once("common.php"); // include the functionality common to all solr tests
/**
 * Tests for the {@link ASolrSearchable} behavior
 * @author Charles Pick / PeoplePerHour.com
 * @package packages.solr.tests
 */
class ASolrSearchableTest extends CTestCase {
	/**
	 * Tests the basic behavior functionality
	 */
	public function testBasics() {
		$behavior = new ASolrSearchable();
		$model = ExampleSolrActiveRecord::model()->find();
		$model->attachBehavior("ASolrSearchable",$behavior);
		$attributeNames = array("id","name","author","popularity","description");
		$this->assertEquals($attributeNames,$behavior->getAttributes());

		$solrDocument = $behavior->getSolrDocument();
		$this->assertTrue($solrDocument instanceof ASolrDocument);
		foreach($attributeNames as $attribute) {
			$this->assertTrue(isset($solrDocument->{$attribute}));
			$this->assertEquals($model->{$attribute},$solrDocument->{$attribute});
		}
		$this->assertTrue($model->index());


	}
	/**
	 * Adds the required data to the test database
	 */
	public function setUp() {
		$this->getConnection();
		foreach($this->fixtureData() as $row) {
			$record = new ExampleSolrActiveRecord();
			foreach($row as $attribute => $value) {
				$record->{$attribute} = $value;
			}
			$this->assertTrue($record->save());
		}
	}
	/**
	 * Deletes the data from the test database
	 */
	public function tearDown() {
		$sql = "DELETE FROM solrexample WHERE 1=1";
		ExampleSolrActiveRecord::model()->getDbConnection()->createCommand($sql)->execute();
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
	 * Generates 50 arrays of attributes for fixtures
	 * @return array the fixture data
	 */
	protected function fixtureData() {
		$rows = array();
		for($i = 0; $i < 50; $i++) {
			$rows[] = array(
				"name" => "Test Item ".$i,
				"popularity" => $i,
				"author" => "Test Author ".$i,
				"description" => str_repeat("lorem ipsum dolor est ",rand(3,20)),

			);
		}
		return $rows;
	}
}

/**
 * An example active record that can be populated from a database or from solr
 * @author Charles Pick / PeoplePerHour.com
 * @package packages.solr.tests
 *
 * @propert integer $id the id field (pk)
 * @property string $name the name field
 * @property string $author the author field
 * @property integer $popularity the popularity field
 * @property string $description the description field
 */
class ExampleSolrActiveRecord extends CActiveRecord {
	/**
	 * Holds the database connection to use with this model
	 * @var CDbConnection
	 */
	protected $_db;
	/**
	 * Gets the database connection to use with this model.
	 * We use an sqlite connection for the test data.
	 * @return CDbConnection the database connection
	 */
	public function getDbConnection() {
		if ($this->_db === null) {
			$dsn = 'sqlite2:'.__DIR__.'/test.db';
			$this->_db = new CDbConnection($dsn);
		}
		return $this->_db;
	}
	/**
	 * Gets the table name to use for this model
	 * @return string the table name
	 */
	public function tableName() {
		return "solrexample";
	}
	/**
	 * Gets the static model instance
	 * @param string $className the class to instantiate
	 * @return ExampleSolrActiveRecord the static model instance
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
}