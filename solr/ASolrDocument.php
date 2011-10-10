<?php
/**
 * Represents an Solr document
 * @author Charles Pick
 * @package packages.solr
 */
class ASolrDocument extends CFormModel implements IteratorAggregate,ArrayAccess,Countable {

	/**
	 * The solr connection
	 * @var ASolrConnection
	 */
	public static $solr;
	/**
	 * The document attributes.
	 * @var CAttributeCollection
	 */
	protected $_attributes;

	/**
	 * The connection to solr
	 * @var ASolrConnection
	 */
	protected $_connection;

	/**
	 * The old primary key value
	 * @var mixed
	 */
	private $_pk;
	/**
	 * Whether this is a new document or not
	 * @var boolean
	 */
	private $_new = true;

	/**
	 * Gets a list of attribute names on the model
	 * @return array the list of attribute names
	 */
	public function attributeNames()
	{
		return CMap::mergeArray(
				array_keys($this->_attributes->toArray()),
				parent::attributeNames()
			);
	}
	/**
	 * An array of attribute default values
	 * @return array
	 */
	public function attributeDefaults() {
		return array();
	}

	/**
	 * Constructor.
	 * @param string $scenario the scenario name
	 * See {@link CModel::scenario} on how scenario is used by models.
	 * @see getScenario
	 */
	public function __construct($scenario = "insert")
	{
		if ($scenario === null) {
			return;
		}
		$this->_attributes = new CAttributeCollection();
		$this->_attributes->caseSensitive = true;
		$this->init();
		$this->attachBehaviors($this->behaviors());
		$this->afterConstruct();
	}

	/**
	 * Returns the solr connection used by solr document.
	 * By default, the "solr" application component is used as the solr connection.
	 * You may override this method if you want to use a different solr connection.
	 * @return ASolrConnection the solr connection used by solr document.
	 */
	public function getSolrConnection()
	{
		if(self::$solr!==null)
			return self::$solr;
		else
		{
			self::$solr=Yii::app()->getDb();
			if(self::$solr instanceof ASolrConnection)
				return self::$solr;
			else
				throw new CDbException(Yii::t('yii','Solr Document requires a "solr" ASolrConnection application component.'));
		}
	}
	/**
	 * Gets the solr input document
	 * @return SolrInputDocument the solr document
	 */
	public function getInputDocument() {
		$document = new SolrInputDocument();
		foreach($this->attributeNames() as $attribute) {
			$document->addField($attribute,$this->{$attribute});
		}
		return $document;
	}
	/**
	 * Returns a property value or an event handler list by property or event name.
	 * This method overrides the parent implementation by returning
	 * a key value if the key exists in the collection.
	 * @param string $name the property name or the event name
	 * @return mixed the property value or the event handler list
	 * @throws CException if the property/event is not defined.
	 */
	public function __get($name)
	{
		if($this->_attributes->contains($name))
			return $this->_attributes->itemAt($name);
		else
			return parent::__get($name);
	}

	/**
	 * Sets value of a component property.
	 * This method overrides the parent implementation by adding a new key value
	 * to the collection.
	 * @param string $name the property name or event name
	 * @param mixed $value the property value or event handler
	 * @throws CException If the property is not defined or read-only.
	 */
	public function __set($name,$value)
	{
		$setter = "set".$name;
		if (method_exists($this,$setter)) {
			return $this->{$setter}($value);
		}
		$this->add($name,$value);
	}

	/**
	 * Checks if a property value is null.
	 * This method overrides the parent implementation by checking
	 * if the key exists in the collection and contains a non-null value.
	 * @param string $name the property name or the event name
	 * @return boolean whether the property value is null
	 * @since 1.0.1
	 */
	public function __isset($name)
	{
		if($this->_attributes->contains($name))
			return $this->_attributes->itemAt($name)!==null;
		else
			return parent::__isset($name);
	}

	/**
	 * Sets a component property to be null.
	 * This method overrides the parent implementation by clearing
	 * the specified key value.
	 * @param string $name the property name or the event name
	 * @since 1.0.1
	 */
	public function __unset($name)
	{
		$this->_attributes->remove($name);
	}
	/**
	 * Initializes this model.
	 * This method is invoked in the constructor right after {@link scenario} is set.
	 * You may override this method to provide code that is needed to initialize the model (e.g. setting
	 * initial property values.)
	 * @since 1.0.8
	 */
	public function init()
	{
	}

	/**
	 * Returns the name of the primary key of the associated solr index.
	 * Child classes should override this if the primary key is anything other than "id"
	 * @return mixed the primary key attribute name(s). Defaults to "id"
	 */
	public function primaryKey()
	{
		return "id";
	}

	/**
	 * Returns the primary key value.
	 * @return mixed the primary key value. An array (column name=>column value) is returned if the primary key is composite.
	 * If primary key is not defined, null will be returned.
	 */
	public function getPrimaryKey()
	{
		$attribute = $this->primaryKey();
		if (!is_array($attribute)) {
			return $this->{$attribute};
		}
		$pk = array();
		foreach($attribute as $field) {
			$pk[$field] = $this->{$attribute};
		}
		return $pk;
	}

	/**
	 * Sets the primary key value.
	 * After calling this method, the old primary key value can be obtained from {@link oldPrimaryKey}.
	 * @param mixed $value the new primary key value. If the primary key is composite, the new value
	 * should be provided as an array (column name=>column value).
	 */
	public function setPrimaryKey($value)
	{
		$this->_pk=$this->getPrimaryKey();
		$attribute = $this->primaryKey();
		if (!is_array($attribute)) {
			return $this->{$attribute} = $value;
		}
		foreach($value as $attribute => $attributeValue) {
			$this->{$attribute} = $attributeValue;
		}
		return $value;
	}
	/**
	 * Returns the old primary key value.
	 * This refers to the primary key value that is populated into the record
	 * after executing a find method (e.g. find(), findAll()).
	 * The value remains unchanged even if the primary key attribute is manually assigned with a different value.
	 * @return mixed the old primary key value. An array (column name=>column value) is returned if the primary key is composite.
	 * If primary key is not defined, null will be returned.
	 * @since 1.1.0
	 */
	public function getOldPrimaryKey()
	{
		return $this->_pk;
	}

	/**
	 * Sets the old primary key value.
	 * @param mixed $value the old primary key value.
	 * @since 1.1.3
	 */
	public function setOldPrimaryKey($value)
	{
		$this->_pk=$value;
	}
	/**
	 * Adds an item into the map.
	 * If the item is an array, it will be converted to an instance of ASolrCriteria
	 * @param mixed $key key
	 * @param mixed $value value
	 */
	public function add($key,$value) {
		if (property_exists($this,$key)) {
			$this->{$key} = $value;
		}
		else {
			$this->_attributes->add($key,$value);
		}
	}

	/**
	 * Returns an array containing the attributes
	 * @return array the list of items in array
	 */
	public function toArray() {
		$data = array();
		foreach($this->attributeNames() as $attribute) {
			$data[$attribute] = $this->{$attribute};
		}
		return $data;
	}

	/**
	 * Returns an array containing the attributes
	 * @see toArray()
	 */
	public function __toArray() {
		return $this->toArray();
	}

	/**
	 * Sets the attribute values in a massive way.
	 * @param array $values attribute values (name=>value) to be set.
	 * @param boolean $safeOnly whether the assignments should only be done to the safe attributes.
	 * A safe attribute is one that is associated with a validation rule in the current {@link scenario}.
	 * @see getSafeAttributeNames
	 * @see attributeNames
	 */
	public function setAttributes($values,$safeOnly=true)
	{
		if ($safeOnly) {
			return parent::setAttributes($values,true);
		}
		foreach($values as $attribute => $value) {
			$this->{$attribute} = $value;
		}
	}

	/**
	 * Saves the solr document
	 * @param boolean $runValidation whether to run validation or not, defaults to true
	 * @return boolean whether the save succeeded or not
	 */
	public function save($runValidation = true) {
		if ($runValidation && !$this->validate()) {
			return false;
		}
		if (!$this->beforeSave()) {
			return false;
		}
		$connection = $this->getSolrConnection();
		$result = $connection->index($this);
	}
	/**
	 * Deletes the solr document
	 * @return boolean whether the delete succeeded or not
	 */
	public function delete() {
		return $this->getType()->delete($this);
	}


	/**
	 * Returns the number of items in the map.
	 * This method is required by Countable interface.
	 * @return integer number of items in the map.
	 */
	public function count()
	{
		return $this->_attributes->getCount();
	}
/**
	 * Returns whether there is an element at the specified offset.
	 * This method is required by the interface ArrayAccess.
	 * @param mixed $offset the offset to check on
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return $this->_attributes->contains($offset);
	}

	/**
	 * Returns the element at the specified offset.
	 * This method is required by the interface ArrayAccess.
	 * @param integer $offset the offset to retrieve element.
	 * @return mixed the element at the offset, null if no element is found at the offset
	 */
	public function offsetGet($offset)
	{
		return $this->_attributes->itemAt($offset);
	}

	/**
	 * Sets the element at the specified offset.
	 * This method is required by the interface ArrayAccess.
	 * @param integer $offset the offset to set element
	 * @param mixed $item the element value
	 */
	public function offsetSet($offset,$item)
	{
		$this->_attributes->add($offset,$item);
	}

	/**
	 * Unsets the element at the specified offset.
	 * This method is required by the interface ArrayAccess.
	 * @param mixed $offset the offset to unset element
	 */
	public function offsetUnset($offset)
	{
		$this->_attributes->remove($offset);
	}

	/**
	 * Returns if the current document is new.
	 * @return boolean whether the record is new and should be inserted when calling {@link save}.
	 * This property is automatically set in constructor and {@link populateRecord}.
	 * Defaults to false, but it will be set to true if the instance is created using
	 * the new operator.
	 */
	public function getIsNewRecord()
	{
		return $this->_new;
	}

	/**
	 * Sets if the record is new.
	 * @param boolean $value whether the document is new and should be inserted when calling {@link save}.
	 * @see getIsNewRecord
	 */
	public function setIsNewRecord($value)
	{
		$this->_new=$value;
	}

	/**
	 * This event is raised before the document is saved.
	 * By setting {@link CModelEvent::isValid} to be false, the normal {@link save()} process will be stopped.
	 * @param CModelEvent $event the event parameter
	 */
	public function onBeforeSave($event)
	{
		$this->raiseEvent('onBeforeSave',$event);
	}

	/**
	 * This event is raised after the document is saved.
	 * @param CEvent $event the event parameter
	 */
	public function onAfterSave($event)
	{
		$this->raiseEvent('onAfterSave',$event);
	}

	/**
	 * This event is raised before the document is deleted.
	 * By setting {@link CModelEvent::isValid} to be false, the normal {@link delete()} process will be stopped.
	 * @param CModelEvent $event the event parameter
	 */
	public function onBeforeDelete($event)
	{
		$this->raiseEvent('onBeforeDelete',$event);
	}

	/**
	 * This event is raised after the record is deleted.
	 * @param CEvent $event the event parameter
	 */
	public function onAfterDelete($event)
	{
		$this->raiseEvent('onAfterDelete',$event);
	}

	/**
	 * This event is raised before a find call.
	 * In this event, the {@link CModelEvent::criteria} property contains the query criteria
	 * passed as parameters to those find methods. If you want to access
	 * the query criteria specified in scopes, please use {@link getDbCriteria()}.
	 * You can modify either criteria to customize them based on needs.
	 * @param CModelEvent $event the event parameter
	 * @see beforeFind
	 */
	public function onBeforeFind($event)
	{
		$this->raiseEvent('onBeforeFind',$event);
	}

	/**
	 * This event is raised after the document is instantiated by a find method.
	 * @param CEvent $event the event parameter
	 */
	public function onAfterFind($event)
	{
		$this->raiseEvent('onAfterFind',$event);
	}

	/**
	 * This method is invoked before saving a document (after validation, if any).
	 * The default implementation raises the {@link onBeforeSave} event.
	 * You may override this method to do any preparation work for document saving.
	 * Use {@link isNewRecord} to determine whether the saving is
	 * for inserting or updating document.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 * @return boolean whether the saving should be executed. Defaults to true.
	 */
	protected function beforeSave()
	{
		if($this->hasEventHandler('onBeforeSave'))
		{
			$event=new CModelEvent($this);
			$this->onBeforeSave($event);
			return $event->isValid;
		}
		else
			return true;
	}

	/**
	 * This method is invoked after saving a document successfully.
	 * The default implementation raises the {@link onAfterSave} event.
	 * You may override this method to do postprocessing after document saving.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 */
	protected function afterSave()
	{
		if($this->hasEventHandler('onAfterSave'))
			$this->onAfterSave(new CEvent($this));
	}

	/**
	 * This method is invoked before deleting a document.
	 * The default implementation raises the {@link onBeforeDelete} event.
	 * You may override this method to do any preparation work for document deletion.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 * @return boolean whether the document should be deleted. Defaults to true.
	 */
	protected function beforeDelete()
	{
		if($this->hasEventHandler('onBeforeDelete'))
		{
			$event=new CModelEvent($this);
			$this->onBeforeDelete($event);
			return $event->isValid;
		}
		else
			return true;
	}

	/**
	 * This method is invoked after deleting a document.
	 * The default implementation raises the {@link onAfterDelete} event.
	 * You may override this method to do postprocessing after the document is deleted.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 */
	protected function afterDelete()
	{
		if($this->hasEventHandler('onAfterDelete'))
			$this->onAfterDelete(new CEvent($this));
	}

	/**
	 * This method is invoked before a find call.
	 * The find calls include {@link find}, {@link findAll}, {@link findByPk},
	 * {@link findAllByPk}, {@link findByAttributes} and {@link findAllByAttributes}.
	 * The default implementation raises the {@link onBeforeFind} event.
	 * If you override this method, make sure you call the parent implementation
	 * so that the event is raised properly.
	 */
	protected function beforeFind()
	{
		if($this->hasEventHandler('onBeforeFind'))
		{
			$event=new CModelEvent($this);
			// for backward compatibility
			$event->criteria=func_num_args()>0 ? func_get_arg(0) : null;
			$this->onBeforeFind($event);
		}
	}

	/**
	 * This method is invoked after each record is instantiated by a find method.
	 * The default implementation raises the {@link onAfterFind} event.
	 * You may override this method to do postprocessing after each newly found record is instantiated.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 */
	protected function afterFind()
	{
		if($this->hasEventHandler('onAfterFind'))
			$this->onAfterFind(new CEvent($this));
	}

	/**
	 * Calls {@link beforeFind}.
	 * This method is internally used.
	 * @since 1.0.11
	 */
	public function beforeFindInternal()
	{
		$this->beforeFind();
	}

	/**
	 * Calls {@link afterFind}.
	 * This method is internally used.
	 * @since 1.0.3
	 */
	public function afterFindInternal()
	{
		$this->afterFind();
	}

}

