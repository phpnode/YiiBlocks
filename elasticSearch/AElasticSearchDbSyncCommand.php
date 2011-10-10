<?php
Yii::import("packages.dataProviderIterator.*");
/**
 * A console command that can be used to synchronize elastic search with a database
 *
 * e.g. ./yiic esdbsync YourModel timeStampAttribute
 * @author Charles Pick
 * @package packages.elasticSearch
 */
class AElasticSearchDbSyncCommand extends CConsoleCommand {
	/**
	 * The class of the model that should be used to find db changes
	 * @var string
	 */
	public $modelClass;
	/**
	 * The scopes on the model to apply when finding db changes
	 * @var array
	 */
	public $scopes = array();

	/**
	 * The name of the attribute that contains the time the row was last updated
	 * This can be an array if more than one attribute should be used,
	 * @var string|array
	 */
	public $updateTimeAttribute = "updatedAt";

	/**
	 * Whether the updatedTimeAttribute uses unix time or not.
	 * Defaults to true.
	 * @var boolean
	 */
	public $useUnixTime = true;

	/**
	 * The number of models to instantiate at one time
	 * @var integer
	 */
	public $chunkSize = 50;
	/**
	 * The time this command was last run
	 * @var integer
	 */
	protected $_lastRunTime;
	/**
	 * The key to use when storing the last run time in the global state
	 * @var string
	 */
	protected $_stateKey;

	/**
	 * @param $args
	 * @return void
	 */
	public function run($args) {
		$startTime = microtime(true);
		$modelClass = array_shift($args);
		if ($modelClass != "") {
			$this->modelClass = $modelClass;
		}
		$updateTimeAttribute = trim(array_shift($args));
		if ($updateTimeAttribute != "") {
			$this->updateTimeAttribute = preg_split("#,#",$updateTimeAttribute,null,PREG_SPLIT_NO_EMPTY);
		}
		$model = $this->applyScopes();

		$dataProvider = new CActiveDataProvider($model,
						array(
							"criteria" => $this->getCriteria(),
							"pagination" => array(
								"pageSize" => $this->chunkSize,
							)
						));
		$iterator = new ADataProviderIterator($dataProvider);

		$total = 0;
		foreach($iterator as $item /* @var CActiveRecord $item */) {
			if ($item->index()) {
				echo "Reindexed ".$this->modelClass." ".$item->getPrimaryKey()."\n";
				$total++;
			}
		}
		// we're done, save the last run time...
		$this->setLastRunTime(isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
		$endTime = microtime(true);
		echo "Indexed ".$total." ".($total == 1 ? $this->modelClass : $this->pluralize($this->modelClass))." in ".($endTime - $startTime)." seconds\n";
	}


	/**
	 * Applys the scopes to the model and returns it
	 * @return CActiveRecord the model with the scopes applied
	 */
	protected function applyScopes() {
		$modelClass = $this->modelClass;
		$model = $modelClass::model();
		foreach($this->scopes as $key => $value) {
			if (is_array($value)) {
				call_user_func_array(array($model, $key),$value);
			}
			else {
				$model->{$value}();
			}
		}
		return $model;
	}
	/**
	 * Gets the criteria to use when finding changes
	 * @return CDbCriteria the criteria to use when finding the changes
	 */
	protected function getCriteria() {
		$criteria = new CDbCriteria();
		$lastRunTime = $this->getLastRunTime();
		if (!$this->useUnixTime) {
			$lastRunTime = date("Y-m-d H:i:s",$lastRunTime);
		}
		$criteria->params[":lastRunTime"] = $lastRunTime;
		if (is_array($this->updateTimeAttribute)) {
			foreach($this->updateTimeAttribute as $attribute) {
				if (!strstr($attribute,".")) {
					$criteria->addCondition("t.".$attribute." > :lastRunTime");
				}
				else {
					$criteria->addCondition($attribute." > :lastRunTime");
				}
			}
		}
		else {
			if (!strstr($this->updateTimeAttribute,".")) {
				$criteria->addCondition("t.".$this->updateTimeAttribute." > :lastRunTime","OR");
			}
			else {
				$criteria->addCondition($this->updateTimeAttribute." > :lastRunTime");
			}
		}
		return $criteria;
	}

	/**
	 * Sets the time this command was last run.
	 * This method stores the value in the global state
	 * @param integer $lastRunTime the time the command was last run
	 * @return integer the time the command was last run
	 */
	public function setLastRunTime($lastRunTime) {
		Yii::app()->setGlobalState($this->getStateKey(),$lastRunTime);
		return $this->_lastRunTime = $lastRunTime;
	}

	/**
	 * Gets the time this command was last run
	 * @return integer the time this command was last run
	 */
	public function getLastRunTime() {
		if ($this->_lastRunTime === null) {
			$this->_lastRunTime = Yii::app()->getGlobalState($this->getStateKey(),1);
		}
		return $this->_lastRunTime;
	}

	/**
	 * Sets the name of the key to use when storing the last run time in the global state
	 * @param string $stateKey the name of the key
	 * @return string the name of the key
	 */
	public function setStateKey($stateKey) {
		return $this->_stateKey = $stateKey;
	}

	/**
	 * Gets the name of the key to use when storing the last run time in the global state
	 * @return string the name of the key
	 */
	public function getStateKey() {
		if ($this->_stateKey === null) {
			$this->_stateKey = get_class($this).":".$this->modelClass.":lastRunTime";
		}
		return $this->_stateKey;
	}


}