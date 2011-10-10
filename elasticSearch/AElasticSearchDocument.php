<?php
/**
 * Represents an Elastic Search document
 * @author Charles Pick
 * @package packages.elasticSearch
 */
class AElasticSearchDocument extends CModel implements IteratorAggregate,ArrayAccess,Countable {

	/**
	 * The unique id for this document
	 * @var mixed
	 */
	protected $_id;

	/**
	 * Holds the document type
	 * @var AElasticSearchDocumentType
	 */
	protected $_type;
	/**
	 * The attribute that should be used as the "name" for this document
	 * @var string
	 */
	protected $_nameAttribute;

	/**
	 * The name of this document
	 * @var string
	 */
	protected $_name;
	/**
	 * The document elements.
	 * A typed list containing document elements
	 * @var CAttributeCollection
	 */
	protected $_attributes;
	/**
	 * The parent document
	 * @var AElasticSearchDocument
	 */
	protected $_parent;

	/**
	 * Holds the version number for this document
	 * @var integer
	 */
	protected $_version;
	/**
	 * Whether this document is new or not
	 * @var boolean
	 */
	protected $_new;
	/**
	 * Gets a list of attribute names on the model
	 * @return array the list of attribute names
	 */
	public function attributeNames() {
		return array_keys($this->_attributes->toArray());
	}
	/**
	 * Sets the attribute values in a massive way.
	 * @param array $values attribute values (name=>value) to be set.
	 * @see getSafeAttributeNames
	 * @see attributeNames
	 */
	public function setAttributes($values) {
		$this->_attributes = new CAttributeCollection();
		$this->_attributes->caseSensitive = false;
		foreach($values as $attribute => $value) {
			$this->add($attribute,$value);
		}
	}
	/**
	 * Declares behaviors to attach to the document
	 * @return array the behaviors to attach to the document
	 */
	public function behaviors() {
		return array(
			"ALinkable" => array(
				"class" => "packages.linkable.ALinkable",
				"controllerRoute" => "/admin/elasticSearch/document/",
				"attributes" => array("index","type","id"),
				"template" => "{name}",
			)
		);
	}
	/**
	 * Constructor.
	 * @param string $scenario The scenario for this model
	 * @param array $data the initial data. .
	 */
	public function __construct($scenario = "insert", $data = null)
	{
		if (!is_string($scenario)) {
			throw new Exception("TEST");
		}
		$this->_attributes = new CAttributeCollection();
		$this->_attributes->caseSensitive = false;
		if (count($data)) {
			foreach($data as $attribute => $value) {
				$this->add($attribute,$value);
			}
		}
		$this->setScenario($scenario);
		$this->init();
		$this->attachBehaviors($this->behaviors());
		$this->afterConstruct();




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
	 * Returns if the current record is new.
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
	 * @param boolean $value whether the record is new and should be inserted when calling {@link save}.
	 * @see getIsNewRecord
	 */
	public function setIsNewRecord($value)
	{
		$this->_new=$value;
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
	 * Adds an item into the map.
	 * If the item is an array, it will be converted to an instance of AElasticSearchCriteria
	 * @param mixed $key key
	 * @param mixed $value value
	 */
	public function add($key,$value) {
		if (is_array($value) && count($value)) {
			if (is_string(array_shift(array_keys($value)))) {
				$value = new AElasticSearchDocument("create", $value);

			}
			else if (is_array(array_shift(array_values($value)))) {
				foreach($value as $i => $item) {
					if (is_array($item)) {
						$value[$i] = new AElasticSearchDocument("create",$item);
						$value[$i]->setName($i);
						$value[$i]->setParent($this);
					}
				}
			}
		}
		if ($value instanceof AElasticSearchDocumentElement) {
			$value->setName($key);
			$value->setParent($this);
		}
		$this->_attributes->add($key,$value);
	}

	/**
	 * @return array the list of items in array
	 */
	public function toArray() {
		$data = array();
		foreach($this->_attributes as $key => $value) {
			if ($value instanceof AElasticSearchDocument) {
				$value = $value->toArray();
			}
			elseif (is_array($value) && array_shift(array_values($value)) instanceof AElasticSearchDocument) {
				foreach($value as $k => $v) {
					if ($v instanceof AElasticSearchDocument) {
						$value[$k] = $v->toArray();
					}
				}
			}
			$data[$key] = $value;
		}
		return $data;
	}


	/**
	 * Sets the document type
	 * @param AElasticSearchDocumentType $type the elastic search document type
	 */
	public function setType($type)
	{
		$this->_type = $type;
	}

	/**
	 * Gets the document type
	 * @return AElasticSearchDocumentType the document type
	 */
	public function getType()
	{
		return $this->_type;
	}
	/**
	 * Gets the search index for this document
	 * @return AElasticSearchIndex the search index for this document
	 */
	public function getIndex() {
		return $this->_type->index;
	}
	/**
	 * Sets the unique id for this document
	 * @param mixed $id the document id
	 */
	public function setId($id) {
		$this->_id = $id;
	}

	/**
	 * Gets the unique id for this document
	 * @return mixed the document id
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * Gets the attributes to show for CDetailView widgets
	 * @return array the attributes to show in a CDetailView widget
	 */
	public function detailViewAttributes() {
		$attributes = array();
		foreach($this->attributeNames() as $attribute) {
			if (!is_array($this->{$attribute}) && !is_object($this->{$attribute})) {
				$attributes[] = $attribute;
			}
		}
		return $attributes;
	}

	/**
	 * Sets the name attribute for this document
	 * @param string $nameAttribute the name attribute
	 */
	public function setNameAttribute($nameAttribute)
	{
		$this->_nameAttribute = $nameAttribute;
	}

	/**
	 * Gets the name attribute for this document
	 * @return string the name attribute
	 */
	public function getNameAttribute()
	{
		if ($this->_nameAttribute === null) {
			$possibleNames = array(
				"name","title","subject","summary","description",
			);
			foreach($this->attributeNames() as $attributeName) {
				foreach($possibleNames as $name) {
					if (stristr($attributeName,$name)) {
						return $this->_nameAttribute = $attributeName;
					}
				}
			}
			$this->_nameAttribute = false;
		}
		return $this->_nameAttribute;
	}

	/**
	 * Sets the name of this document, this will not be persisted!
	 * @param string $name the document name
	 */
	public function setName($name)
	{
		$this->_name = $name;
	}

	/**
	 * Gets the name of this document
	 * @return string the document name
	 */
	public function getName()
	{
		if ($this->_name === null) {
			$nameAttribute = $this->getNameAttribute();
			if ($nameAttribute !== false) {
				$this->_name = $this->{$nameAttribute};
			}
			else {
				$this->_name = $this->getId();
			}
		}
		return $this->_name;
	}
	/**
	 * Saves the elastic search document
	 * @param boolean $runValidation whether to run validation or not, defaults to true
	 * @return boolean whether the save succeeded or not
	 */
	public function save($runValidation = true) {
		if ($runValidation && !$this->validate()) {
			return false;
		}
		$response =  $this->getType()->index($this);
		if (!$response->ok) {
			return false;
		}
		$this->setId($response->_id);
		$this->setVersion($response->_version);
		return true;

	}
	/**
	 * Deletes the elastic search document
	 * @return boolean whether the delete succeeded or not
	 */
	public function delete() {
		return $this->getType()->delete($this);
	}

	/**
	 * Returns an iterator for traversing the items in the list.
	 * This method is required by the interface IteratorAggregate.
	 * @return CMapIterator an iterator for traversing the items in the list.
	 */
	public function getIterator()
	{
		return $this->_attributes->getIterator();
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
	 * Sets the parent for this item
	 * @param AElasticSearchDocument $parent the parent document
	 */
	public function setParent($parent)
	{
		$this->_parent = $parent;
	}

	/**
	 * Gets the parent document
	 * @return AElasticSearchDocument the parent document
	 */
	public function getParent()
	{
		return $this->_parent;
	}

	/**
	 * Sets the version number for this document
	 * @param integer $version the version number
	 */
	public function setVersion($version)
	{
		$this->_version = $version;
	}

	/**
	 * Gets the version number for this document
	 * @return integer the version number
	 */
	public function getVersion()
	{
		return $this->_version;
	}
}