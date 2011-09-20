<?php
/**
 * Ensures that each model gets loaded only once by keeping every loaded model in a map.
 *
 * @author Charles Pick
 * @package packages.identityMap
 */
class AIdentityMap extends CTypedMap {
	/**
	 * Constructor, sets the type for the map
	 */
	public function __construct() {
		parent::__construct("CActiveRecord");
	}

	/**
	 * Whether the map is enabled or not
	 * @var boolean
	 */
	public $enabled = true;
	/**
	 * Gets a key to use to store models with the given attributes
	 * @param string|array $pk the name of the primary key attribute(s)
	 * @param array $attributes the attributes that contain the keys
	 * @return string the key key to use when storing and retrieving models with these attributes
	 */
	public function getKey($pk, array $attributes) {
		if (is_array($pk)) {
			$limit = count($pk);
			$key = array();
			for($i = 0; $i < $limit; $i++) {
				$key[] = $attributes[$pk[$i]];
			}
			$pk = implode(":",$key);
		}
		else {
			$pk = $attributes[$pk];
		}
		return $pk;
	}
}