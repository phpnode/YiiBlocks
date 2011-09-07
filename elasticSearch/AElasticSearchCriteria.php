<?php
/**
 * Represents an elastic search query criteria
 * @package packages.elasticSearch
 * @author Charles Pick
 */
class AElasticSearchCriteria extends CAttributeCollection {
	/**
	 * Sets value of a component property.
	 * @param string $name the property name or event name
	 * @param mixed $value the property value or event handler
	 * @throws CException If the property is not defined or read-only.
	 */
	public function __set($name,$value)
	{
		$methodName = "set".$name;
		if (method_exists($this,$methodName)) {
			return $this->{$methodName}($value);
		}
		return parent::__set($name,$value);
	}
	public function __call($name, $parameters) {
		if (count($parameters) == 1 && is_array($parameters[0])) {
			$parameters = array_shift($parameters);
		}
		if (isset($this[$name])) {
			$parameters = CMap::mergeArray($this>itemAt($name)->toArray(),$parameters);
		}
		$this->add($name, new AElasticSearchCriteria($parameters));

		return $this->itemAt($name);
	}

	public function getLimit() {
		return $this->itemAt("size");
	}
	public function setLimit($value) {
		$this->add("size",$value);
	}
	public function getOffset() {
		return $this->itemAt("from");
	}
	public function setOffset($value) {
		$this->add("from",$value);
	}
	/**
	 * Adds an item into the map.
	 * If the item is an array, it will be converted to an instance of AElasticSearchCriteria
	 * @param mixed $key key
	 * @param mixed $value value
	 */
	public function add($key,$value)
	{
		if (is_array($value) && count($value) && is_string(array_shift(array_keys($value)))) {
			$value = new AElasticSearchCriteria($value);
		}
		parent::add($key,$value);

	}

	/**
	 * @return array the list of items in array
	 */
	public function toArray()
	{
		$data = array();
		foreach(parent::toArray() as $key => $value) {
			if ($value instanceof AElasticSearchCriteria) {
				$value = $value->toArray();
			}
			$data[$key] = $value;
		}

		return $data;
	}
}