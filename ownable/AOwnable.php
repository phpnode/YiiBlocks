<?php
/**
 * A behavior that allows models to be "owned" by other models.
 * <pre>
 * $ownable = new AOwnable;
 * $ownable->ownerClassName = "User"; // blog posts are owned by their authors
 * $ownable->keyAttribute = "authorId"; // the name of the column in the blogpost table that refers to the author
 * $ownable->attribute = "author"; // the name of the attribute that will be used to access the author
 * $blogPost = new BlogPost;
 * $blogPost->attachBehavior("ownable", $ownable);
 * $blogPost->author = User::model()->findByPK(Yii::app()->user->id);
 * $blogPost->save(); // blog post is now owned by the currently logged in user
 * // 
 * </pre>
 */
class AOwnable extends CActiveRecordBehavior implements IAOwnable {
	/**
	 * The attribute that should be used to access the owner object.
	 * Defaults to "owner".
	 * @see __get()
	 * @see __set()
	 * @var string
	 */
	public $attribute = "owner";
	
	/**
	 * The key attribute in the child model that refers to the owner.
	 * This property is required!
	 * @var string
	 */
	public $keyAttribute;
	
	/**
	 * The owner object.
	 * @see getOwner()
	 * @see setOwner()
	 * @see __get()
	 * @see __set()
	 * @var CActiveRecord
	 */
	protected $_owner;

	
	/**
	 * The class name of the owner.
	 * @see getOwnerClassName()
	 * @see setOwnerClassName()
	 * @var string
	 */
	protected $_ownerClassName;
	
	/**
	 * Gets the id of the owner for this model.
	 * This method is required by the IAOwnable interface
	 * @return integer the owner id
	 */
	public function getOwnerId() {
		return $this->owner->{$this->keyAttribute};
	}
	/**
	 * Sets the owner id for this model. 
	 * @param integer $id The owner id
	 */
	public function setOwnerId($id) {
		return $this->owner->{$this->keyAttribute} = $id;
	
	}
	/**
	 * Gets the class name of the owner model.
	 * This method is required by the IAOwnable interface.
	 * @return string The model class name
	 */
	public function getOwnerClassName() {
		return $this->_ownerClassName;
	}
	/**
	 * Sets the class name of the owner model.
	 * @param string $className the model class name
	 */
	public function setOwnerClassName($className) {
		return $this->_ownerClassName = $className;
	}
	/**
	 * Gets the owner model if it exists, otherwise null is returned.
	 * @return CActiveRecord the owner model, or null if the model doesn't exist.
	 */
	public function getOwnerModel() {
		if ($this->_owner === null) {
			$className = $this->getOwnerClassName();
			$this->_owner = $className::model()->findByPk($this->getOwnerId());
		}
		return $this->_owner;
	}
	/**
	 * Sets the owner model.
	 * @param CActiveRecord $owner the model that owns this one
	 */
	public function setOwnerModel($owner) {
		$this->setOwnerId($owner->primaryKey);
		return $this->_owner = $owner;
	}
	
	/**
	 * Determines whether a property can be read.
	 * A property can be read if the class has a getter method
	 * for the property name. Note, property name is case-insensitive.
	 * @param string $name the property name
	 * @return boolean whether the property can be read
	 * @see canSetProperty
	 */
	public function canGetProperty($name)
	{
		if ($name == $this->attribute) {
			return true;
		}
		return parent::canGetProperty($name);
	}

	/**
	 * Determines whether a property can be set.
	 * A property can be written if the class has a setter method
	 * for the property name. Note, property name is case-insensitive.
	 * @param string $name the property name
	 * @return boolean whether the property can be written
	 * @see canGetProperty
	 */
	public function canSetProperty($name)
	{
		if ($name == $this->attribute) {
			return true;
		}
		return parent::canSetProperty($name);
	}
	
	/**
	 * Magic getter to allow the owner to be accessed like a property on the model.
	 * @see getOwnerModel()
	 */
	public function __get($name) {
		if ($name == $this->attribute) {
			return $this->getOwnerModel();			
		}
		return parent::__get($name);
	}
	
	/**
	 * Magic setter to allow the owner to be accessed like a property on the model.
	 * @see setOwnerModel()
	 */
	public function __set($name,$value) {
		if ($name == $this->attribute) {
			return $this->setOwnerModel($value);			
		}
		return parent::__set($name,$value);
	}
	
	/**
	 * Magic isset to allow the owner to be accessed like a property on the model.
	 * @see getOwnerModel()
	 */
	public function __isset($name) {
		if ($name == $this->attribute) {
			return $this->getOwnerModel() !== null;			
		}
		return parent::__isset($name);
	}
	
	/**
	 * Magic unset to allow the owner to be accessed like a property on the model.
	 * @see setOwnerModel()
	 */
	public function __unset($name) {
		if ($name == $this->attribute) {
			return $this->setOwnerModel(null);			
		}
		return parent::__unset($name);
	}
}
