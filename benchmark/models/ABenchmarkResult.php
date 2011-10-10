<?php
/**
 * Represents a result for a benchmark.
 * @author Charles Pick
 * @package packages.benchmark.models
 *
 *
 * @property integer $id The id of the benchmark result
 * @property integer $benchmarkId The id of the benchmark
 * @property float $initialLoadAverage The load average on the system before the benchmark runs
 * @property float $finalLoadAverage The load average after the benchmark runs
 * @property string $serverSoftware The detected server software
 * @property string $serverHostname The hostname of the server
 * @property integer $serverPort The port of the server
 * @property string $documentPath The path to the document on the server
 * @property integer $documentSize The size of the document in bytes
 * @property integer $concurrency The number of concurrent requests
 * @property float $duration The duration of the test in seconds
 * @property integer $completedRequests the total number of completed requests
 * @property integer $failedRequests The total number of failed requests
 * @property integer $failedOnConnect The number of requests that failed due to connection errors
 * @property integer $failedOnReceive The number of requests that failed when receiving data
 * @property integer $failedOnLength The number of requests that failed due to invalid lengths
 * @property integer $failedOnException The number of requests that failed due to exceptions
 * @property integer $writeErrors The number of write errors
 * @property integer $totalTransferred The total number of bytes transferred
 * @property integer $htmlTransferred The total amount of HTML transferred in bytes
 * @property float $requestsPerSecond The number of requests per second
 * @property float $timePerRequest The average time per request in ms
 * @property float $longestRequest The time of the longest request
 * @property float $transferRate The transfer rate in bytes / second
 * @property integer $timeAdded the unix time this benchmark result was added
 *
 * @property ABenchmark $benchmark The benchmark this result belongs to
 */
class ABenchmarkResult extends CActiveRecord {
	/**
	 * The previous result
	 * @var ABenchmarkResult
	 */
	protected $_previous;
	/**
	 * The next result
	 * @var ABenchmarkResult
	 */
	protected $_next;
	/**
	 * Gets the db connection to use for this model
	 * @return CDbConnection the connection to use for benchmarks
	 */
	public function getDbConnection() {
		return Yii::app()->getModule("admin")->getModule("benchmark")->getDb();
	}
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return ABenchmarkResult the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Returns the name of the associated database table.
	 * @see CActiveRecord::tableName()
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return "benchmarkresults";
	}

	/**
	 * Returns the validation rules for attributes.
	 * @see CModel::rules()
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(

		);
	}

	/**
	 * Returns the relational rules that specify the relations this model uses
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'benchmark' => array(self::BELONGS_TO, "ABenchmark","benchmarkId"),
		);
	}
	/**
	 * The beforeSave event, sets the time added for new records
	 * @return boolean whether the saving process should continue
	 */
	public function beforeSave() {
		if ($this->isNewRecord) {
			$this->timeAdded = (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
		}
		return parent::beforeSave();
	}
	/**
	 * Gets the previous result
	 * @return ABenchmarkResult the previous result
	 */
	public function getPrevious() {
		if ($this->_previous === null) {
			$criteria = new CDbCriteria;
			$criteria->condition = "benchmarkId = :benchmarkId AND id < :id";
			$criteria->params = array(
				":id" => $this->id,
				":benchmarkId" => $this->benchmarkId,
			);
			$criteria->order = "id DESC";
			$this->_previous = self::model()->find($criteria);
		}
		return $this->_previous;
	}

	/**
	 * Gets the next result
	 * @return ABenchmarkResult the next result
	 */
	public function getNext() {
		if ($this->_next === null) {
			$criteria = new CDbCriteria;
			$criteria->condition = "benchmarkId = :benchmarkId AND id > :id";
			$criteria->params = array(
				":id" => $this->id,
				":benchmarkId" => $this->benchmarkId,
			);
			$criteria->order = "id ASC";
			$this->_next = self::model()->find($criteria);
		}
		return $this->_next;
	}

	/**
	 * Determines whether this result is a performance regression or not
	 * @return boolean true if this is a regression
	 */
	public function getIsRegression($attribute = "requestsPerSecond") {
		if (!is_object($this->getPrevious())) {
			return false;
		}
		return ($this->getDifference($attribute) <= -7);
	}

	/**
	 * Determines whether this result is a performance improvement or not
	 * @return boolean true if this is an improvement
	 */
	public function getIsProgression($attribute = "requestsPerSecond") {
		if (!is_object($this->getPrevious())) {
			return false;
		}
		return ($this->getDifference($attribute) >= 7);
	}

	/**
	 * Gets the percentage difference of the given attribute of this result and the previous result
	 * @param string $attribute the name of the attribute to compare
	 * @return float
	 */
	public function getDifference($attribute = "requestsPerSecond") {
		if (!is_object($this->getPrevious())) {
			return 0;
		}
		return ((($this->{$attribute} - $this->getPrevious()->{$attribute}) / $this->getPrevious()->{$attribute}) * 100);
	}

}