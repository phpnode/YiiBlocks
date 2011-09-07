<?php

class AElasticSearchActiveDataProvider extends CActiveDataProvider {
	protected $_criteria;
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



		$data=$this->model->elasticSearch($criteria);
		return $data;
	}

	/**
	 * Calculates the total number of data items.
	 * @return integer the total number of data items.
	 */
	protected function calculateTotalItemCount()
	{
		return $this->model->asa("AElasticSearchable")->count($this->getCriteria());
	}
}