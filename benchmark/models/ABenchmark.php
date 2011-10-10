<?php
/**
 * Represents a benchmark for a URL or route.
 * @author Charles Pick
 * @package packages.benchmark.models
 *
 *
 * @property integer $id The id of the benchmark
 * @property string $url The URL to benchmark
 * @property string $route The route to benchmark
 * @property array $params The parameters to add to the URL or route
 * @property integer $timeAdded the unix time this benchmark was added
 *
 * @property ABenchmarkResult[] $results the results for this benchmark
 * @property ABenchmarkResult $lastResult the latest result for this benchmark
 * @property ABenchmarkResult $penultimateResult the second to last result for this benchmark
 */
class ABenchmark extends CActiveRecord {
	/**
	 * Gets the behaviors to attach to this model
	 * @return array the behaviors to attach to this model
	 */
	public function behaviors() {
		return array(
			"ASerializedAttribute" => array(
				"class" => "packages.serializedAttribute.ASerializedAttributeBehavior",
				"attributes" => array("params"),
			),
			"ALinkable" => array(
				"class" => "packages.linkable.ALinkable",
				"controllerRoute" => "/admin/benchmark/benchmark",
			)
		);
	}
	/**
	 * Gets the URL for the benchmark
	 * @return string The URL for the benchmark
	 */
	public function getUrl() {
		if ($this->url != "") {
			return $this->url;
		}
		return Yii::app()->createAbsoluteUrl($this->route, (is_array($this->params) ? $this->params : array()));
	}

	/**
	 * Gets the db connection to use for this model
	 * @return CDbConnection the connection to use for benchmarks
	 */
	public function getDbConnection() {
		return Yii::app()->getModule("admin")->getModule("benchmark")->getDb();
	}
	/**
	 * Gets the sparkline data for this item
	 * @return array
	 */
	public function getSparklineData() {
		$command = $this->getDbConnection()->createCommand("SELECT requestsPerSecond FROM benchmarkresults WHERE benchmarkId = :benchmarkId");
		$command->bindValue(":benchmarkId",$this->id);
		return $command->queryColumn();
	}
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return ABenchmark the static model class
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
		return "benchmarks";
	}

	/**
	 * Returns the validation rules for attributes.
	 * @see CModel::rules()
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('url','url'),
			array('route,params','safe'),
			// The following rule is used by search().
			array('url, route', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * Returns the relational rules that specify the relations this model uses
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'results' => array(self::HAS_MANY, "ABenchmarkResult","benchmarkId"),
			'lastResult' => array(self::HAS_ONE, "ABenchmarkResult","benchmarkId", "order" => "lastResult.id DESC"),
			'penultimateResult' => array(self::HAS_ONE, "ABenchmarkResult","benchmarkId", "order" => "penultimateResult.id DESC", "condition" => "penultimateResult.id != (SELECT id FROM benchmarkresults WHERE benchmarkId = penultimateResult.benchmarkId ORDER BY id DESC limit 1)"),
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
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		$criteria=new CDbCriteria;

		$criteria->compare('url',$this->url,true);
		$criteria->compare('route',$this->route, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	/**
	 * Gets the percentage difference of the given attribute of the last result and the penultimate result
	 * @param string $attribute the name of the attribute to compare
	 * @return float
	 */
	public function getDifference($attribute = "requestsPerSecond") {
		if (!is_object($this->lastResult) || !is_object($this->penultimateResult)) {
			return 0;
		}
		return ((($this->lastResult->{$attribute} - $this->penultimateResult->{$attribute}) / $this->penultimateResult->{$attribute}) * 100);
	}

	/**
	 * Determines whether the last result is a performance regression or not
	 * @return boolean true if the last result is a regression
	 */
	public function getIsRegression($attribute = "requestsPerSecond") {
		return ($this->getDifference($attribute) <= -7);
	}

	/**
	 * Determines whether the last result is a performance improvement or not
	 * @return boolean true if the last result is an improvement
	 */
	public function getIsProgression($attribute = "requestsPerSecond") {
		return ($this->getDifference($attribute) >= 7);
	}
}