<?php
/**
 * Represents information about a class property in the documentation
 * @author Charles Pick
 * @package packages.docs.models
 *
 * @property boolean $isStatic whether this is a static property or not
 * @property boolean $isPublic whether this is a public property or not
 * @property boolean $isProtected whether this is a protected property or not
 * @property boolean $isPrivate whether this is a private property or not
 */
class ADocumentationPropertyEntity extends ADocumentationEntity {
	/**
	 * Returns the static model instance
	 * @param string $className the name of the model class
	 * @return ADocumentationPropertyEntity the model instance
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	/**
	 * Gets the possible types for this property
	 * @return ADocumentationEntity[] the possible types
	 */
	public function getTypes() {
		return $this->getChildren(); // possibly change in future
	}
}