<?php
/**
 * Represents information about a class in the documentation
 * @author Charles Pick
 * @package packages.docs.models
 */
class ADocumentationClassEntity extends ADocumentationEntity {
	/**
	 * Returns the static model instance
	 * @param string $className the name of the model class
	 * @return ADocumentationClassEntity the model instance
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * Gets the constants that belong to this class
	 * @return ADocumentationConstEntity[] the constants that belong to this class
	 */
	public function getConstants() {
		$className = "ADocumentationConstEntity";
		$items = array();
		foreach($this->getChildren() as $item) {
			if ($item->modelClass == $className) {
				$items[$item->name] = $item;
			}
		}
		return $items;
	}

	/**
	 * Gets the properties that belong to this class
	 * @return ADocumentationPropertyEntity[] the properties that belong to this class
	 */
	public function getProperties() {
		$className = "ADocumentationPropertyEntity";
		$items = array();
		foreach($this->getChildren() as $item) {
			if ($item->modelClass == $className) {
				$items[$item->name] = $item;
			}
		}
		return $items;
	}

	/**
	 * Gets the methods that belong to this class
	 * @return ADocumentationMethodEntity[] the methods that belong to this class
	 */
	public function getMethods() {
		$className = "ADocumentationMethodEntity";
		$items = array();
		foreach($this->getChildren() as $item) {
			if ($item->modelClass == $className) {
				$items[$item->name] = $item;
			}
		}
		return $items;
	}
}