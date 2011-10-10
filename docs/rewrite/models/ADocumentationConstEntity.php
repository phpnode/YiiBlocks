<?php
/**
 * Represents information about a class constant in the documentation
 * @author Charles Pick
 * @package packages.docs.models
 */
class ADocumentationConstEntity extends ADocumentationEntity {
	/**
	 * Returns the static model instance
	 * @param string $className the name of the model class
	 * @return ADocumentationConstEntity the model instance
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

}