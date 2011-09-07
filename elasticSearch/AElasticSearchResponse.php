<?php
/**
 * Holds a response from elastic search
 * @author Charles Pick
 * @package packages.elasticSearch
 */
class AElasticSearchResponse extends CAttributeCollection {
	/**
	 * @var boolean whether the keys are case-sensitive. Defaults to false.
	 */
	public $caseSensitive=true;
	/**
	 * Adds an item into the map.
	 * If the item is an array, it will be converted to an instance of AElasticSearchResponse
	 * @param mixed $key key
	 * @param mixed $value value
	 */
	public function add($key,$value)
	{
		if (is_array($value) && count($value) && is_string(array_shift(array_keys($value)))) {
			$value = new AElasticSearchResponse($value);
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
			if ($value instanceof AElasticSearchResponse) {
				$value = $value->toArray();
			}
			$data[$key] = $value;
		}
		return $data;
	}
}