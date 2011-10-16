<?php
/**
 * A behavior that can be attached to models to generate plausible random data for testing
 * @author Charles Pick
 * @package packages.testDataGenerator
 */
class ATestDataGenerator extends CBehavior {
	/**
	 * Holds the total number of countries in the seed database
	 * @var integer
	 */
	const TOTAL_COUNTRIES = 239;

	/**
	 * The entropy level to use when generating results.
	 * This should be a number from 1 to 100 where 100 is the most random
	 * @var float
	 */
	public $entropy = 10.0;
	/**
	 * The database connection to use to access the seed data
	 * @var CDbConnection
	 * @see getDb
	 */
	protected $_db;

	/**
	 * Generates test data based on the owner model
	 */
	public function generateTestData() {
		$this->generateUserDetails();
	}

	protected function checkDependencies() {

	}
	/**
	 * Generates an array of random details for a user
	 * @return array the user details
	 */
	public function generateUserDetails() {
		$attributes = array();
		if (rand(0,100) <= 51) {
			$attributes['gender'] = "female";
			$attributes['firstname'] = $this->generateFemaleFirstName();
		}
		else {
			$attributes['gender'] = "male";
			$attributes['firstname'] = $this->generateMaleFirstName();
		}
		$attributes['surname'] = $this->generateSurname();
		$attributes['location'] = $this->generateLocationInfo();
		print_r($attributes);
	}
	/**
	 * Generates a male first name
	 * @return string the male name
	 */
	public function generateMaleFirstName() {
		$command = $this->getDb()->createCommand("SELECT name FROM malenames ORDER BY RANDOM() LIMIT 1");
		return $command->queryScalar();
	}
	/**
	 * Generates a female first name
	 * @return string the female name
	 */
	public function generateFemaleFirstName() {
		$command = $this->getDb()->createCommand("SELECT name FROM femalenames ORDER BY RANDOM() LIMIT 1");
		return $command->queryScalar();
	}
	/**
	 * Generates a surname
	 * @return string the surname
	 */
	public function generateSurname() {
		$command = $this->getDb()->createCommand("SELECT name FROM surnames ORDER BY RANDOM() LIMIT 1");
		return $command->queryScalar();
	}
	/**
	 * Generates location info for a random point
	 * @return CAttributeCollection collection containing country and city
	 */
	public function generateLocationInfo() {
		$location = new CAttributeCollection();
		$limit = round(($this->entropy / 100) * self::TOTAL_COUNTRIES);
		$sql = "SELECT * FROM (SELECT * FROM countries ORDER BY gnp DESC LIMIT ".$limit.") ORDER BY RANDOM() LIMIT 1";
		$command = $this->getDb()->createCommand($sql);
		$location->country = new CAttributeCollection($command->queryRow());

		$command = $this->getDb()->createCommand("SELECT COUNT(*) FROM cities WHERE countryCode = :code");
		$command->bindValue(":code",$location->country->code);
		$totalCities = $command->queryScalar();
		$limit = round(($this->entropy / 100) * $totalCities);
		$sql = "SELECT * FROM (SELECT * FROM cities WHERE countryCode = :code ORDER BY population DESC LIMIT ".$limit.") ORDER BY RANDOM() LIMIT 1";
		$command = $this->getDb()->createCommand($sql);
		$command->bindValue(":code",$location->country->code);
		$location->city = new CAttributeCollection($command->queryRow());
		return new CAttributeCollection($location);
	}

	public function generateStreetName() {

	}


	/**
	 * Sets the database connection to use to access the seed data
	 * @param CDbConnection $db the database connection for the seed data
	 */
	public function setDb($db)
	{
		$this->_db = $db;
	}

	/**
	 * Gets the database connection to use to access the seed data.
	 * @return CDbConnection the database connection for the seed data
	 */
	public function getDb()
	{
		if ($this->_db === null) {
			$dsn = 'sqlite2:'.__DIR__.'/seed.db';
			$this->_db = new CDbConnection($dsn);
		}
		return $this->_db;
	}


}