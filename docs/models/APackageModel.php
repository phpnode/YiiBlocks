<?php
/**
 * Represents information about a package to be documentated.
 * @property integer $id The package primary key
 * @property string $name The package name
 * @property string $introduction The introduction for this package
 * @property string $description The description for this package
 * 
 * @author Charles Pick
 * @package packages.docs.models
 */
class ANamespaceModel extends ADocsModel {
	

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
		return "packages";
	}
	
	/**
	 * Returns the relational rules that specify the relations this model uses
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			"namespaces" => array(self::BELONGS_TO,"ANamespaceModel","packageId"),
			"interfaces" => array(self::HAS_MANY,"AInterfaceModel","packageId"),
			"classes" => array(self::HAS_MANY,"AClassModel","packageId"),
			"functions" => array(self::HAS_MANY,"AFunctionModel","packageId"),
		);
	}
}
