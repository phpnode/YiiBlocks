<?php
/**
 * Represents information about an interface to be documentated.
 * @property integer $id The interface primary key
 * @property string $name The interface name
 * @property string $introduction The introduction for this interface
 * @property string $description The description for this interface
 * @property integer $parentId The id of the interface that this one extends 
 * @property integer $namespaceId The namespace that this interface belongs to
 * @property integer $packageId The ID of the package this interface belongs to
 * @property boolean $isFinal Whether this is a final interface or not
 * @property boolean $isAbstract Whether this is an abstract interface or not
 * @property integer $fileId The file that this property is declared in
 * @property integer $startLine The start line for this property declaration
 * @property integer $endLine The end line for this property declaration
 * 
 * @author Charles Pick
 * @package packages.docs.models
 */
class AInterfaceModel extends ADocsModel {
	
	/**
	 * Creates an active record instance.
	 * @param array $attributes list of attribute values for the active records.
	 * @return AInterfaceModel|AClassModel either an interface or class depending on the value of isInterface
	 */
	protected function instantiate($attributes)
	{
		if ($attributes['isInterface']) {
			$class = "AInterfaceModel";
		}
		else {
			$class = "AClassModel";
		}
		$model=new $class(null);
		return $model;
	}
	
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
	 * Returns the name of the associated database table.
	 * @see CActiveRecord::tableName()
	 * @return string the associated database table name
	 */
	public function tableName() {
		return "classes";
	}
	
	/**
	 * Returns the relational rules that specify the relations this model uses
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			"namespace" => array(self::BELONGS_TO,"ANamespaceModel","namespaceId"),
			"parent" => array(self::BELONGS_TO,get_class($this),"parentId"),
			"children" => array(self::HAS_MANY, get_class($this), "parentId"),
			"constants" => array(self::HAS_MANY,"AConstantModel", "classId"),
			"methods" => array(self::HAS_MANY,"AMethodModel","classId"),
			"file" => array(self::BELONGS_TO,"AFileModel","fileId"),
		);
	}
	
	
}
