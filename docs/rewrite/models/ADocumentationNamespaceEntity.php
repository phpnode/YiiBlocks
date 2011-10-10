<?php
/**
 * Represents information about a namespace in the documentation
 * @author Charles Pick
 * @package packages.docs.models
 */
class ADocumentationNamespaceEntity extends ADocumentationEntity {
	/**
	 * Whether this namespace uses curly bracket syntax or not.
	 * This will not be persisted!
	 * @var boolean
	 */
	public $isCurly = false;
	/**
	 * Returns the static model instance
	 * @param string $className the name of the model class
	 * @return ADocumentationNamespaceEntity the model instance
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * Gets the classes that belong to this namespace
	 * @return ADocumentationClassEntity[] the classes that belong to this namespace
	 */
	public function getClasses() {
		$className = "ADocumentationClassEntity";
		$items = array();
		foreach($this->getChildren() as $item) {
			if ($item->modelClass == $className) {
				$items[] = $item;
			}
		}
		return $items;
	}

	/**
	 * Gets the interfaces that belong to this namespace
	 * @return ADocumentationInterfaceEntity[] the interfaces that belong to this namespace
	 */
	public function getInterfaces() {
		$className = "ADocumentationInterfaceEntity";
		$items = array();
		foreach($this->getChildren() as $item) {
			if ($item->modelClass == $className) {
				$items[] = $item;
			}
		}
		return $items;
	}

	/**
	 * Gets the functions that belong to this namespace
	 * @return ADocumentationFunctionEntity[] the functions that belong to this namespace
	 */
	public function getFunctions() {
		$className = "ADocumentationFunctionEntity";
		$items = array();
		foreach($this->getChildren() as $item) {
			if ($item->modelClass == $className) {
				$items[] = $item;
			}
		}
		return $items;
	}
}