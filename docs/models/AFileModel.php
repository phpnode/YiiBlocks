<?php
/**
 * Represents information about a source code file to be documentated.
 * @property integer $id The file id
 * @property string $path The path to the file
 * @property string $hash The hash of the file contents
 * @property integer $timeModified The time the file was last modified, used when checking if files are in sync
 * @author Charles Pick
 * @package packages.docs.models
 */
class AFileModel extends ADocsModel {

	
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
		return "files";
	}
	
	/**
	 * Returns the relational rules that specify the relations this model uses
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			"classes" => array(self::HAS_MANY,"AClassModel","classId","condition" => "interfaces.isInterface = 0"),
			"functions" => array(self::HAS_MANY,"AFunctionModel","classId"),
			"interfaces" => array(self::HAS_MANY,"AInterfaceModel","classId","condition" => "interfaces.isInterface = 1"),
		);
	}
}
