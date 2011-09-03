<?php
/**
 * Represents information about a class method to be documentated.
 * @property integer $id The method primary key
 * @property string $name The method name
 * @property string $introduction The introduction for this method
 * @property string $description The description for this method
 * @property integer $classId The id of the class this method belongs to
 * @property boolean $isFinal Whether this is a final method or not
 * @property boolean $isAbstract Whether this is an abstract method or not
 * @property boolean $isPublic Whether this is a public method or not
 * @property boolean $isProtected Whether this is a protected method or not
 * @property boolean $isPrivate Whether this is a private method or not
 * @property boolean $isStatic Whether this is a static method or not
 * @property integer $packageId The ID of the package
 * @property integer $fileId The file that this method is declared in
 * @property integer $startLine The start line for this method declaration
 * @property integer $endLine The end line for this method declaration
 * 
 * @author Charles Pick
 * @package packages.docs.models
 */
class AMethodModel extends AFunctionModel {
	
	
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
			"class" => array(self::BELONGS_TO,"AInterfaceModel","classId"),
		));
	}
}
