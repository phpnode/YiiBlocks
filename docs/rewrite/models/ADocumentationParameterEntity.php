<?php
/**
 * Represents information about a method or function parameter in the documentation
 * @author Charles Pick
 * @package packages.docs.models
 */
class ADocumentationParameterEntity extends ADocumentationEntity {
	/**
	 * Returns the static model instance
	 * @param string $className the name of the model class
	 * @return ADocumentationParameterEntity the model instance
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	/**
	 * Gets the possible types for this method
	 * @return ADocumentationEntity[] the possible types
	 */
	public function getTypes() {
		$classNames = array(
			"ADocumentationInterfaceEntity",
			"ADocumentationClassEntity",
		);
		$items = array();
		foreach($this->getChildren() as $item) {
			if (in_array($item->modelClass,$classNames)) {
				$items[] = $item;
			}
		}
		return $items;
	}

}