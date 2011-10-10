<?php
/**
 * Represents information about a function in the documentation
 * @author Charles Pick
 * @package packages.docs.models
 */
class ADocumentationFunctionEntity extends ADocumentationEntity {
	/**
	 * Returns the static model instance
	 * @param string $className the name of the model class
	 * @return ADocumentationFunctionEntity the model instance
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * Gets the possible types for this function
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

	/**
	 * Gets the parameters that belong to this function
	 * @return ADocumentationParameterEntity[] the parameters that belong to this function
	 */
	public function getParameters() {
		$className = "ADocumentationParameterEntity";
		$items = array();
		foreach($this->getChildren() as $item) {
			if ($item->modelClass == $className) {
				$items[] = $item;
			}
		}
		return $items;
	}
}