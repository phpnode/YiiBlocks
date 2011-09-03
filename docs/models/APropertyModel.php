<?php
/**
 * Represents information about a property to be documentated.
 * 
 * @property integer $id The property primary key
 * @property string $name The property name
 * @property string $introduction The introduction for this property
 * @property string $description The description for this property
 * @property integer $classId The id of the class
 * @property boolean $isPublic Whether this is a public property or not
 * @property boolean $isProtected Whether this is a protected property or not
 * @property boolean $isPrivate Whether this is a private property or not
 * @property boolean $isStatic Whether this is a static property or not
 * @property string $type The property type
 * @property string $defaultValue The default value for this property
 * @property integer $packageId The ID of the package
 * @property integer $fileId The file that this property is declared in
 * @property integer $startLine The start line for this property declaration
 * @property integer $endLine The end line for this property declaration
 * 
 * @author Charles Pick
 * @package packages.docs.models
 */
class APropertyModel extends ADocsModel {

	
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
		return "properties";
	}
	
	/**
	 * Returns the relational rules that specify the relations this model uses
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			"class" => array(self::BELONGS_TO,"AClassModel","classId"),
		);
	}
}
