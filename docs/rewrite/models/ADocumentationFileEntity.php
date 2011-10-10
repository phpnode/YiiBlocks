<?php
/**
 * Represents information about a source code file
 * @author Charles Pick
 * @package packages.docs
 *
 * @property string $value The file name
 */
class ADocumentationFileEntity extends ADocumentationEntity {
	/**
	 * Returns the static model instance
	 * @param string $className the name of the model class
	 * @return ADocumentationFileEntity the model instance
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	/**
	 * Gets the primary root to use for this entity
	 * @return ADocumentationHierarchy the primary root node
	 */
	public function getRoot() {
		return ADocumentationHierarchy::getFileRoot();
	}
}