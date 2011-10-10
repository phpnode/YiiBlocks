<?php
/**
 * Represents information about a namespace to be documentated.
 * @property integer $id The namespace primary key
 * @property string $name The namespace name
 * @property string $introduction The introduction for this namespace
 * @property string $description The description for this namespace
 * @property integer $namespaceId The id of the parent namespace, if any
 * @property integer $packageId The ID of the package
 * @property integer $fileId The file that this namespace is declared in
 * @property integer $startLine The start line for this namespace declaration
 * @property integer $endLine The end line for this namespace declaration
 *
 * @author Charles Pick
 * @package packages.docs.models
 */
class ANamespaceModel extends ADocsModel {


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return ANamespaceModel the static model class
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
		return "namespaces";
	}

	/**
	 * Returns the relational rules that specify the relations this model uses
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			"parent" => array(self::BELONGS_TO,"ANamespaceModel","namespaceId"),
			"file" => array(self::BELONGS_TO,"AFileModel","fileId"),
			"children" => array(self::BELONGS_TO,"ANamespaceModel","namespaceId"),
			"interfaces" => array(self::HAS_MANY,"AInterfaceModel","namespaceId"),
			"classes" => array(self::HAS_MANY,"AClassModel","namespaceId"),
			"functions" => array(self::HAS_MANY,"AFunctionModel","namespaceId"),
		);
	}

	public function findByName($name) {
		if (strstr($name, "\\")) {
			foreach(explode("\\",$name) as $name) {

			}
		}
	}

}
