<?php

abstract class AActiveRecord extends CActiveRecord {
	/**
	 * The identity map
	 * @var AIdentityMap
	 */
	private $_identityMap;

	/**
	 * Creates an active record with the given attributes.
	 * This method is internally used by the find methods.
	 * @param array $attributes attribute values (column name=>column value)
	 * @param boolean $callAfterFind whether to call {@link afterFind} after the record is populated.
	 * This parameter is added in version 1.0.3.
	 * @return CActiveRecord the newly created active record. The class of the object is the same as the model class.
	 * Null is returned if the input data is false.
	 */
	public function populateRecord($attributes,$callAfterFind=true)
	{
		if($attributes!==false)
		{
			$identityMap = $this->getIdentityMap();
			if (!$identityMap->enabled) {
				return parent::populateRecord($attributes,$callAfterFind);
			}
			$pk = $this->getMetaData()->tableSchema->primaryKey;
			$key = $identityMap->getKey($pk, $attributes);

			if (!$identityMap->contains($key)) {
				$identityMap->add($key, parent::populateRecord($attributes,$callAfterFind));
			}
			else {
				Yii::log("Loading ".get_class($identityMap->itemAt($key))."#$key from identity map");
			}
			return $identityMap->itemAt($key);
		}
		else
			return null;
	}

	/**
	 * Gets the identity map for this model class
	 * @return AIdentityMap the identity map
	 */
	public function getIdentityMap() {
		$class = get_class($this);
		if ($this !== $class::model()) {
			return $class::model()->getIdentityMap();
		}
		if ($this->_identityMap === null) {
			$this->_identityMap = new AIdentityMap;
		}
		return $this->_identityMap;
	}
	/**
	 * Sets the identity map for this model class
	 * @param AIdentityMap|array $identityMap the identity map
	 */
	public function setIdentityMap($identityMap) {
		$class = get_class($this);
		if ($this !== $class::model()) {
			return $class::model()->setIdentityMap($identityMap);
		}
		if (!($identityMap instanceof AIdentityMap)) {
			$identityMap = new AIdentityMap($identityMap);
		}
		$this->_identityMap = $identityMap;
	}


}