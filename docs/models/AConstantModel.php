<?php
/**
 * Represents information about a constant to be documentated.
 * @property integer $id The constant primary key
 * @property string $name The constant name
 * @property string $introduction The introduction for this constant
 * @property string $description The description for this constant
 * @property integer $classId The id of the class this constant belongs to
 * @property string $type The constant type
 * @property string $defaultValue The default value for this constant
 * @property integer $packageId The ID of the package this constant is defined in
 * @property integer $fileId The file that this constant is declared in
 * @property integer $startLine The start line for this constant declaration
 * @property integer $endLine The end line for this constant declaration
 * 
 * @author Charles Pick
 * @package packages.docs.models
 */
class AConstantModel extends ADocsModel {

	
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
		return "constants";
	}
	
	/**
	 * Returns the relational rules that specify the relations this model uses
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			"class" => array(self::BELONGS_TO,"AClassModel","classId"),
			"file" => array(self::BELONGS_TO,"AFileModel","fileId"),
		);
	}
}
