<?php
/**
 * Adds dynamic properties to objects to allow easy access to resources
 * <pre>
 * $user = User::model()->findByPk(1);
 * $resourceful = new AResourceful;
 * $resourceful->attributes = array(
 * 		"thumbnail" => array(
 * 				"fileTypes" => array("jpg","png","gif")
 * 			),
 * 		"photos" => array(
 * 			"fileTypes" => array("jpg","png","gif"),
 * 			"multiple" => true,
 * 		)
 * 	);
 * $user->attachBehavior("AResourceful",$resourceful);
 * echo $user->thumbnail->url."<br />";
 * foreach($user->photos as $photo) {
 * 	echo $photo->link."<br />";
 * }
 * 
 * </pre>
 * @package packages.resources
 * @author Charles Pick
 */
class AResourceful extends CActiveRecordBehavior {
	/**
	 * An array of resource attributes that will become virtual properties on the owner object.
	 * <pre>
	 * array(
	 * 	"attachments" => array(
	 * 		"multiple" => true,
	 * 		"fileTypes" => array("doc","xls","pdf","jpg","png","gif","ppt"),
	 * 	),
	 * "thumbnail" => array(
	 * 		"fileTypes" => array("doc")
	 * 	)
	 * )
	 * </pre>
	 * @var AResourceAttribute[]
	 */
	protected $_attributes = array();
	
	/**
	 * Attaches the behavior to the owner
	 * @see CActiveRecordBehavior::attach()
	 * @param CComponent $component The component to attach to
	 */
	public function attach($component) {
		parent::attach($component);
		foreach($this->_attributes as $name => $attribute) {
			$attribute->owner = $this->owner;
		}
	}
	
	/**
	 * Gets the attributes that will become virtual properties on the owner model
	 * @return AResourceAttribute[]
	 */
	public function getAttributes() {
		return $this->_attributes;
	}
	
	/**
	 * Sets the attributes that will become virtual properties on the owner model
	 * @param array $attributes The resource attributes for the owner model
	 * @return AResourceAttribute[] the resource attributes
	 */
	public function setAttributes($attributes) {
		$this->_attributes = array();
		foreach($attributes as $name => $config) {
			if (!is_array($config)) {
				$name = $config;
				$config = array();
			}
			$this->_attributes[$name] = new AResourceAttribute;
			$this->_attributes[$name]->owner = $this->owner;
			
			foreach($config as $attribute => $value) {
				$this->_attributes[$name]->{$attribute} = $value;
			}
		}
		return $this->_attributes;
	}
	
	/**
	 * Determines whether a property can be read.
	 * A property can be read if the class has a getter method
	 * for the property name. 
	 * @param string $name the property name
	 * @return boolean whether the property can be read
	 * @see canSetProperty
	 */
	public function canGetProperty($name) {
		return method_exists($this,'get'.$name) || isset($this->attributes[$name]);
	}

	/**
	 * Determines whether a property can be set.
	 * A property can be written if the class has a setter method
	 * for the property name.
	 * @param string $name the property name
	 * @return boolean whether the property can be written
	 * @see canGetProperty
	 */
	public function canSetProperty($name) {
		return method_exists($this,'set'.$name) || isset($this->attributes[$name]);
	}
	/**
	 * The magic getter, enables resources to be accessed like properties
	 * @param string $name The name of the virtual property
	 */
	public function __get($name) {
		if (isset($this->attributes[$name])) {
			if ($this->attributes[$name]->multiple) {
				return $this->attributes[$name]->resources;
			}
			else {
				$resources = $this->attributes[$name]->resources;
				return array_shift($resources);
			}
		}
		return parent::__get($name);
	}
	
	/**
	 * The magic setter, enables resources to be accessed like properties
	 * @param string $name The name of the virtual property
	 * @param mixed $value The value to assign to the virtual property
	 */
	public function __set($name, $value) {
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name]->add($value);
		}
		return parent::__set($name, $value);
	}
	
	/**
	 * The magic isset, enables resources to be accessed like properties
	 * @param string $name The name of the virtual property
	 */
	public function __isset($name) {
		if (isset($this->attributes[$name])) {
			return true;
		}
		return parent::__isset($name);
	}
	
	/**
	 * The magic unset, enables resources to be accessed like properties
	 * @param string $name The name of the virtual property
	 */
	public function __unset($name) {
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name]->delete();
		}
		return parent::__unset($name);
	}
	
}

/**
 * Holds information about a virtual property on a {@link AResourceful resourceful} model
 * @package packages.resources
 * @author Charles Pick
 */
class AResourceAttribute extends CComponent {
	/**
	 * The model that this resource belongs to
	 * @var CModel
	 */
	public $owner;
	
	/**
	 * An array of allowed file extensions
	 * e.g.
	 * <pre>
	 * $this->fileTypes = array("doc","ppt","xls","png","jpg","gif");
	 * </pre>
	 * @var array
	 */
	public $fileTypes = array();
	
	/**
	 * Whether this attribute contains multiple resources or not.
	 * Defaults to false
	 * @var boolean
	 */
	public $multiple = false;
	
	/**
	 * The name of this resource attribute
	 * @var string
	 */
	public $name;
	/**
	 * Holds the resources for this attribute
	 * @var AResource[]
	 */
	protected $_resources;
	
	/**
	 * Adds a resource to this attribute
	 * @param AResource|CUploadedFile $item the resource, or an uploaded file
	 * @return boolean whether the resource was added or not
	 */
	public function add($item) {
		if ($item instanceof CUploadedFile) {
			$item = AResource::fromUploadedFile($item);
		}
		$this->getResources();
		
		$item->ownerModel = get_class($this->owner);
		$item->ownerId = $this->owner->primaryKey;
		$item->ownerAttribute = $this->name;
		
		if (!$item->save()) {
			return false;
		}
		
		if (!$this->multiple) {
			foreach($this->_resources as $resource) {
				$resource->delete();
			}
		}
		$this->_resources[] = $item;
		return true;
	}
	/**
	 * Deletes the resource(s) associated with this attribute
	 */
	public function delete() {
		foreach($this->resources as $resource) {
			$resource->delete();
		}
	}
	
	/**
	 * Gets the resources for this attribute
	 * @return AResource[] the resources that belong to this owner attribute
	 */
	public function getResources() {
		if ($this->_resources === null) {
			$this->_resources = AResource::model()->findAllByAttributes(array(
				"ownerModel" => get_class($this->owner),
				"ownerId" => $this->owner->id,
				"ownerAttribute" => $this->name
			));
		}
		return $this->_resources;
	}
	
}
