<?php
/**
 * Represents information about a function or method parameter to be documentated.
 * @property integer $id The parameter primary key
 * @property string $name The parameter name
 * @property string $introduction The introduction for this parameter
 * @property string $description The description for this parameter
 * @property string $type The parameter type
 * @property string $defaultValue The default value for this parameter
 * @property integer $functionId The ID of the function / method
 * 
 * @author Charles Pick
 * @package packages.docs.models
 */
class AParameterModel extends ADocsModel {

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
		return "parameters";
	}
	
	/**
	 * Returns the relational rules that specify the relations this model uses
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			"function" => array(self::BELONGS_TO,"AFunctionModel","functionId"),
		);
	}
}
