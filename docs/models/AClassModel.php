<?php
/**
 * Represents information about a class to be documentated.
 * @property integer $id The class primary key
 * @property string $name The class name
 * @property string $introduction The introduction for this class
 * @property string $description The description for this class
 * @property integer $parentId The id of the class that this one extends 
 * @property integer $namespaceId The namespace that this class belongs to
 * @property integer $packageId The ID of the package this class belongs to
 * @property boolean $isFinal Whether this is a final class or not
 * @property boolean $isAbstract Whether this is an abstract class or not
 * @property integer $fileId The file that this property is declared in
 * @property integer $startLine The start line for this property declaration
 * @property integer $endLine The end line for this property declaration
 * 
 * @author Charles Pick
 * @package packages.docs.models
 */
class AClassModel extends AInterfaceModel {
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return ADocsModel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Returns the relational rules that specify the relations this model uses
	 * @return array relational rules.
	 */
	public function relations() {
		return CMap::mergeArray(parent::relations(), array(
			"properties" => array(self::HAS_MANY,"APropertyModel","classId"),
			"interfaces" => array(self::MANY_MANY,"AInterfaceModel","classImplements(classId,implementsId)"),
		));
	}
}
