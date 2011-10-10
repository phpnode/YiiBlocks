<?php
/**
 * Represents an solr query criteria
 * @package packages.solr
 * @author Charles Pick
 */
class ASolrCriteria extends SolrQuery {

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

}