<?php
/**
 * Represents an Elastic Search document
 * @author Charles Pick
 * @package packages.elasticSearch
 */
class AElasticSearchDocument extends CAttributeCollection {

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
	 * @return array the events
	 */
	public function events() {
		return array();
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
	 * Initializes the list with an array or an iterable object.
	 * @param array $data the intial data. Default is null, meaning no initialization.
	 * @param boolean $readOnly whether the list is read-only
	 * @throws CException If data is not null and neither an array nor an iterator.
	 */
	public function __construct($data=null,$readOnly=false) {
		parent::__construct($data,$readOnly);
		foreach($this->behaviors() as $name => $behavior) {
			$this->attachBehavior($name,$behavior);
		}
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
				$value = new AElasticSearchDocument($value);
			}
			else if (is_array(array_shift(array_values($value)))) {
				foreach($value as $i => $item) {
					if (is_array($item)) {
						$value[$i] = new AElasticSearchDocument($item);
					}
				}
			}
		}

		if($this->caseSensitive)
			parent::add($key,$value);
		else
			parent::add(strtolower($key),$value);
	}

	/**
	 * @return array the list of items in array
	 */
	public function toArray() {
		$data = array();
		foreach(parent::toArray() as $key => $value) {
			if ($value instanceof AElasticSearchDocument) {
				$value = $value->toArray();
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
	public function setId($id)
	{
		$this->_id = $id;
	}

	/**
	 * Gets the unique id for this document
	 * @return mixed the document id
	 */
	public function getId()
	{
		return $this->_id;
	}
	/**
	 * Gets the attribute names for this document
	 * @return array the attribute names for this document
	 */
	public function attributeNames() {
		return array_keys($this->toArray());
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
	 * Returns a value indicating whether the attribute is required.
	 * Always false
	 * @param string $attribute attribute name
	 * @return boolean whether the attribute is required
	 * @since 1.0.2
	 */
	public function isAttributeRequired($attribute) {
		return false;
	}

	/**
	 * Generates a user friendly attribute label.
	 * This is done by replacing underscores or dashes with blanks and
	 * changing the first letter of each word to upper case.
	 * For example, 'department_name' or 'DepartmentName' becomes 'Department Name'.
	 * @param string $name the column name
	 * @return string the attribute label
	 */
	public function generateAttributeLabel($name)
	{
		return ucwords(trim(strtolower(str_replace(array('-','_','.'),' ',preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $name)))));
	}

	/**
	 * Returns the text label for the specified attribute.
	 * @param string $attribute the attribute name
	 * @return string the attribute label
	 * @see generateAttributeLabel
	 * @see attributeLabels
	 */
	public function getAttributeLabel($attribute)
	{
		return $this->generateAttributeLabel($attribute);
	}

	
}