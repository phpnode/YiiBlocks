<?php
/**
 * Represents an solr query criteria
 * @package packages.solr
 * @author Charles Pick / PeoplePerHour.com
 */
class ASolrCriteria extends SolrQuery {

	/**
	 * Constructor
	 * @param array|null $data the parameters to initialize the criteria with
	 */
	public function __construct($data = null) {
		parent::__construct();
		if ($data !== null) {
			foreach($data as $key => $value) {
				$this->{$key} = $value;
			}
		}
	}
	/**
	 * Returns a property value based on its name.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using the following syntax to read a property
	 * <pre>
	 * $value=$component->propertyName;
	 * </pre>
	 * @param string $name the property name
	 * @return mixed the property value
	 * @throws CException if the property is not defined
	 * @see __set
	 */
	public function __get($name) {
		$getter='get'.$name;
		if(method_exists($this,$getter)) {
			return $this->$getter();
		}
		throw new CException(Yii::t('yii','Property "{class}.{property}" is not defined.',
			array('{class}'=>get_class($this), '{property}'=>$name)));
	}

	/**
	 * Sets value of a component property.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using the following syntax to set a property
	 * <pre>
	 * $this->propertyName=$value;
	 * </pre>
	 * @param string $name the property name
	 * @param mixed $value the property value
	 * @return mixed
	 * @throws CException if the property is not defined or the property is read only.
	 * @see __get
	 */
	public function __set($name,$value)
	{
		$setter='set'.$name;
		if(method_exists($this,$setter)) {
			return $this->$setter($value);
		}
		if(method_exists($this,'get'.$name))
			throw new CException(Yii::t('yii','Property "{class}.{property}" is read only.',
				array('{class}'=>get_class($this), '{property}'=>$name)));
		else
			throw new CException(Yii::t('yii','Property "{class}.{property}" is not defined.',
				array('{class}'=>get_class($this), '{property}'=>$name)));
	}

	/**
	 * Checks if a property value is null.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using isset() to detect if a component property is set or not.
	 * @param string $name the property name
	 * @return boolean
	 */
	public function __isset($name)
	{
		$getter='get'.$name;
		return method_exists($this,$getter);
	}

	/**
	 * Sets a component property to be null.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using unset() to set a component property to be null.
	 * @param string $name the property name or the event name
	 * @throws CException if the property is read only.
	 * @return mixed
	 * @since 1.0.1
	 */
	public function __unset($name)
	{
		$setter='set'.$name;
		if(method_exists($this,$setter))
			$this->$setter(null);
		else if(method_exists($this,'get'.$name))
			throw new CException(Yii::t('yii','Property "{class}.{property}" is read only.',
				array('{class}'=>get_class($this), '{property}'=>$name)));
	}

	/**
	 * Gets the number of items to return.
	 * This method is required for compatibility with pagination
	 * @return integer the number of items to return
	 */
	public function getLimit() {
		return $this->getRows();
	}
	/**
	 * Sets the number of items to return.
	 * This method is required for compatibility with pagination
	 * @param integer $value the number of items to return
	 */
	public function setLimit($value) {
		$this->setRows($value);
	}

	/**
	 * Gets the starting offset when returning results
	 * This method is required for compatibility with pagination
	 * @return integer the starting offset
	 */
	public function getOffset() {
		return $this->getStart();
	}
	/**
	 * Sets the starting offset when returning results
	 * This method is required for compatibility with pagination
	 * @param integer $value the starting offset
	 */
	public function setOffset($value) {
		return $this->setStart($value);
	}

	/**
	 * Appends a condition to the existing {@link query}.
	 * The new condition and the existing condition will be concatenated via the specified operator
	 * which defaults to 'AND'.
	 * The new condition can also be an array. In this case, all elements in the array
	 * will be concatenated together via the operator.
	 * This method handles the case when the existing condition is empty.
	 * After calling this method, the {@link query} property will be modified.
	 * @param mixed $condition the new condition. It can be either a string or an array of strings.
	 * @param string $operator the operator to join different conditions. Defaults to 'AND'.
	 * @return ASolrCriteria the criteria object itself
	 */
	public function addCondition($condition,$operator='AND')
	{
		if(is_array($condition))
		{
			if($condition===array())
				return $this;
			$condition='('.implode(') '.$operator.' (',$condition).')';
		}
		if($this->getQuery()===null)
			$this->setQuery($condition);
		else
			$this->setQuery('('.$this->getQuery().') '.$operator.' ('.$condition.')');
		return $this;
	}


	/**
	 * Appends an IN condition to the existing {@link query}.
	 * The IN condition and the existing condition will be concatenated via the specified operator
	 * which defaults to 'AND'.
	 *
	 * @param string $column the column name
	 * @param array $values list of values that the column value should be in
	 * @param string $operator the operator used to concatenate the new condition with the existing one.
	 * Defaults to 'AND'.
	 * @return ASolrCriteria the criteria object itself
	 */
	public function addInCondition($column,$values,$operator='AND')
	{
		if(($n=count($values))<1)
			return $this;
		if($n===1)
		{
			$value=reset($values);

			$condition=$column.':'.$value;
		}
		else
		{
			$params=array();
			foreach($values as $value)
			{
				$params[]=$value;
			}
			$condition=$column.':('.implode(' OR ',$params).')';
		}
		return $this->addCondition($condition,$operator);
	}

	/**
	 * Appends an NOT IN condition to the existing {@link query}.
	 * The NOT IN condition and the existing condition will be concatenated via the specified operator
	 * which defaults to 'AND'.
	 * @param string $column the column name (or a valid SQL expression)
	 * @param array $values list of values that the column value should not be in
	 * @param string $operator the operator used to concatenate the new condition with the existing one.
	 * Defaults to 'AND'.
	 * @return ASolrCriteria the criteria object itself
	 */
	public function addNotInCondition($column,$values,$operator='AND')
	{
		if(($n=count($values))<1)
			return $this;
		if($n===1)
		{
			$value=reset($values);

			$condition=$column.':!'.$value;
		}
		else
		{
			$params=array();
			foreach($values as $value)
			{
				$params[]="!".$value;
			}
			$condition=$column.':('.implode(' AND ',$params).')';
		}
		return $this->addCondition($condition,$operator);
	}


	/**
	 * Merges this criteria with another
	 * @param ASolrCriteria $criteria the criteria to merge with
	 * @return ASolrCriteria the merged criteria
	 */
	public function mergeWith(ASolrCriteria $criteria) {
		$methodList = array(
			"getParams" => "addParam",
			"getFields" => "addField",
			"getFacetDateFields" => "addFacetDateField",
			"getFacetFields" => "addFacetField",
			"getFacetQueries" => "addFacetQuery",
			"getHighlightFields" => "addHighlightField",
			"getMltFields" => "addMltField",
			"getMltQueryFields" => "addMltQueryField",
			"getFilterQueries" => "addFilterQuery",
			"getSortFields" => "addSortField",
			"getStatsFields" => "addStatsField",
			"getStatsFacets" => "addStatsFacet",

		);
		$reflection = new ReflectionClass($criteria);
		foreach($reflection->getMethods() as $method) {
			$methodName = $method->getName();
			if (substr($methodName,0,3) !== "get" || $method->getDeclaringClass()->name !== "SolrQuery") {
				continue;
			}
			$setter = "s".substr($methodName,1);
			if (!method_exists($criteria,$setter)) {
				if (isset($methodList[$methodName])) {

					$adder = $methodList[$methodName];

					$result = $criteria->{$methodName}();
					if ($result === null) {
						continue;
					}
					foreach($result as $key => $value) {
						$this->{$adder}($value);
					}
				}
			}
			elseif ($method->getNumberOfRequiredParameters() == 0) {

				$value = $criteria->{$methodName}();
				if ($value === null) {
					continue;
				}
				if ($methodName != "getQuery") {
					$this->{$setter}($value);
				}
				else {
					$currentValue = $this->{$methodName}();
					if ($currentValue === null) {
						$this->{$setter}($value);
					}
					else {
						$this->{$setter}($currentValue." AND ".$value);
					}
				}
			}
		}
		return $this;

	}
}