<?php

class AElasticSearchDataProvider extends CActiveDataProvider {
	/**
	 * Holds the key attribute
	 * @var string
	 */
	public $keyAttribute = "position";
	protected $_criteria;
	/**
	 * Constructor.
	 * @param mixed $modelClass the model class (e.g. 'Post') or the model finder instance
	 * (e.g. <code>Post::model()</code>, <code>Post::model()->published()</code>).
	 * @param array $config configuration (name=>value) to be applied as the initial property values of this class.
	 */
	public function __construct($modelClass,$config=array())
	{
		if($modelClass instanceof AElasticSearchDocumentType) {
			$this->modelClass=get_class($modelClass);
			$this->model=$modelClass;

			$this->setId($this->modelClass);
			foreach($config as $key=>$value) {
				$this->$key=$value;
			}
		}
		else {
			parent::__construct($modelClass,$config);
		}
	}
	/**
	 * Returns the query criteria.
	 * @return AElasticSearchCriteria the query criteria
	 */
	public function getCriteria()
	{
		if($this->_criteria===null)
			$this->_criteria=new AElasticSearchCriteria();
		return $this->_criteria;
	}

	/**
	 * Sets the query criteria.
	 * @param mixed $value the query criteria. This can be either a AElasticSearchCriteria object or an array
	 * representing the query criteria.
	 */
	public function setCriteria($value)
	{
		$this->_criteria=$value instanceof AElasticSearchCriteria ? $value : new AElasticSearchCriteria($value);
	}
	/**
	 * Fetches the data from the persistent data storage.
	 * @return array list of data items
	 */
	protected function fetchData()
	{
		$criteria=clone $this->getCriteria();

		if(($pagination=$this->getPagination())!==false)
		{
			$pagination->setItemCount($this->getTotalItemCount());
			$pagination->applyLimit($criteria);
		}



		$data=$this->model->search($criteria);
		if ($pagination !== false) {
			$pagination->setItemCount($data->total);
		}
		$this->setTotalItemCount($data->total);
		return $data;
	}

	/**
	 * Calculates the total number of data items.
	 * @return integer the total number of data items.
	 */
	protected function calculateTotalItemCount()
	{
		return $this->model->count($this->getCriteria());
	}
}