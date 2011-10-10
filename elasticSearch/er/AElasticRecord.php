<?php
/**
 * AElasticRecord is an ActiveRecord like interface to elastic search
 * It allows documents to be indexed, edited, searched and deleted.
 * @author Charles Pick
 * @package packages.elasticSearch
 */
class AElasticRecord extends CActiveRecord {
	/**
	 * the default elastic search connection for all elastic record classes.
	 * By default, this is the 'elasticSearch' application component.
	 * @see getElasticSearchConnection()
	 */
	public static $elasticSearch;
	/**
	 * The static model instances
	 * @var AElasticRecord[]
	 */
	private static $_models=array();

	/**
	 * Holds the elastic record meta data
	 * @var AElasticRecordMetaData
	 */
	private $_md;

	/**
	 * Holds the search criteria
	 * @var AElasticSearchCriteria
	 */
	private $_c;
	/**
	 * Holds the attibutes for the model
	 * @var array
	 */
	private $_attributes=array();
	/**
	 * Gets the name of the document type.
	 * Defaults to the class name.
	 * @return string the name of the document type
	 */
	public function type() {
		return get_class($this);
	}

	/**
	 * Gets the name of the index to use for this document type
	 * Defaults to the connection default.
	 * @return string
	 */
	public function indexName() {
		return $this->getDbConnection()->defaultIndex;
	}

	/**
	 * Returns the elastic search connection used by elastic record.
	 * By default, the "elasticSearch" application component is used as the elastic search connection.
	 * You may override this method if you want to use a different elastic search connection.
	 * @return AElasticSearchConnection the database connection used by elastic record.
	 */
	public function getDbConnection() {
		if(self::$db!==null)
			return self::$db;
		else
		{
			self::$db=Yii::app()->elasticSearch;
			if(self::$db instanceof AElasticSearchConnection) {
				return self::$db;
			}
			else {
				throw new CDbException(Yii::t('packages.elasticSearch','Elastic Record requires a "elasticSearch" AElasticSearchConnection application component.'));
			}
		}
	}

	/**
	 * Returns the query criteria associated with this model.
	 * @param boolean $createIfNull whether to create a criteria instance if it does not exist. Defaults to true.
	 * @return AElasticSearchCriteria the query criteria that is associated with this model.
	 * This criteria is mainly used by {@link scopes named scope} feature to accumulate
	 * different criteria specifications.
	 */
	public function getDbCriteria($createIfNull=true)
	{
		if($this->_c===null)
		{
			if(($c=$this->defaultScope())!==array() || $createIfNull) {
				$this->_c=new AElasticSearchCriteria($c);
			}
		}
		return $this->_c;
	}

	/**
	 * Sets the query criteria for the current model.
	 * @param AElasticSearchCriteria $criteria the query criteria
	 */
	public function setDbCriteria(AElasticSearchCriteria $criteria)
	{
		$this->_c=$criteria;
	}


	/**
	 * Returns the static model of the specified Elastic Record class.
	 * The model returned is a static instance of the ER class.
	 * It is provided for invoking class-level methods (something similar to static class methods.)
	 *
	 * EVERY derived ER class must override this method as follows,
	 * <pre>
	 * public static function model($className=__CLASS__)
	 * {
	 *     return parent::model($className);
	 * }
	 * </pre>
	 *
	 * @param string $className active record class name.
	 * @return AElasticRecord elastic record model instance.
	 */
	public static function model($className=__CLASS__)
	{
		if(isset(self::$_models[$className]))
			return self::$_models[$className];
		else
		{
			$model=self::$_models[$className]=new $className(null);
			$model->_md=new AElasticRecordMetaData($model);
			$model->attachBehaviors($model->behaviors());
			return $model;
		}
	}


	/**
	 * Returns the meta-data for this ER
	 * @return AElasticRecordMetaData the meta for this ER class.
	 */
	public function getMetaData()
	{
		if($this->_md!==null)
			return $this->_md;
		else
			return $this->_md=self::model(get_class($this))->_md;
	}

	/**
	 * Refreshes the meta data for this ER class.
	 * By calling this method, this ER class will regenerate the meta data needed.
	 * This is useful if the document schema has been changed and you want to use the latest
	 * available document schema. Make sure you have called {@link AElasticSearchDocumentType::refresh}
	 * before you call this method. Otherwise, old document schema data will still be used.
	 */
	public function refreshMetaData()
	{
		$finder=self::model(get_class($this));
		$finder->_md=new AElasticRecordMetaData($finder);
		if($this!==$finder) {
			$this->_md=$finder->_md;
		}
	}

	/**
	 * PHP sleep magic method.
	 * This method ensures that the model meta data reference is set to null.
	 * @return array
	 */
	public function __sleep()
	{
		$this->_md=null;
		return array_keys((array)$this);
	}

	/**
	 * Sets the named attribute value.
	 * You may also use $this->AttributeName to set the attribute value.
	 * @param string $name the attribute name
	 * @param mixed $value the attribute value.
	 * @return boolean whether the attribute exists and the assignment is conducted successfully
	 * @see hasAttribute
	 */
	public function setAttribute($name,$value) {
		if(property_exists($this,$name)) {
			$this->$name=$value;
		}
		else {

		}
		return true;
	}
}