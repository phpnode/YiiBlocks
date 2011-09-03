<?php
/**
 * Represents information about a function to be documentated.
 * @property integer $id The function primary key
 * @property string $name The function name
 * @property string $introduction The introduction for this function
 * @property string $description The description for this function
 * @property integer $packageId The ID of the package
 * @property integer $fileId The file that this function is declared in
 * @property integer $startLine The start line for this function declaration
 * @property integer $endLine The end line for this function declaration
 * 
 * @author Charles Pick
 * @package packages.docs.models
 */
class AFunctionModel extends ADocsModel {
	
	/**
	 * Creates an active record instance.
	 * @param array $attributes list of attribute values for the active records.
	 * @return AFunctionModel|AMethodModel either a function or method depending on the value of classId
	 */
	protected function instantiate($attributes)
	{
		if ($attributes['classId']) {
			$class = "AMethodModel";
		}
		else {
			$class = "AFunctionModel";
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
		return "functions";
	}
	
	/**
	 * Returns the relational rules that specify the relations this model uses
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			"namespace" => array(self::BELONGS_TO,"ANamespaceModel","namespaceId"),
			"parameters" => array(self::HAS_MANY,"AParameterModel","functionId"),
			"file" => array(self::BELONGS_TO,"AFileModel","fileId"),
		);
	}
}
