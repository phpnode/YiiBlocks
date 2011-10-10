<?php
/**
 * Holds a documentation hierarchy
 *
 * @author Charles Pick
 * @package packages.docs.models
 *
 * @property integer $id the id of the node
 * @property integer $root the if of the root for this node
 * @property integer $lft the left value for the node
 * @property integer $rgt the right value for the node
 * @property integer $level the level of the node
 * @property integer $entityId the documentation entity this node refers to
 */
class ADocumentationHierarchy extends CActiveRecord {
	const FILE_ROOT = 1;
	const PACKAGE_ROOT = 2;
	const INTERFACE_ROOT = 3;
	const NAMESPACE_ROOT = 4;

	/**
	 * Holds the file root node
	 * @var ADocumentationHierarchy
	 */
	protected static $_fileRoot;
	/**
	 * Holds the package root node
	 * @var ADocumentationHierarchy
	 */
	protected static $_packageRoot;
	/**
	 * Holds the interface root node
	 * @var ADocumentationHierarchy
	 */
	protected static $_interfaceRoot;
	/**
	 * Holds the namespace root node
	 * @var ADocumentationHierarchy
	 */
	protected static $_namespaceRoot;

	/**
	 * Returns the static model instance
	 * @param string $className the name of the model class
	 * @return ADocumentationHierarchy the model instance
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	/**
	 * Gets the file root node, if it doesn't exist it will be created
	 * @return ADocumentationHierarchy the file root node
	 */
	public static function getFileRoot() {
		if (self::$_fileRoot === null) {
			self::$_fileRoot = self::model()->findByPk(self::FILE_ROOT);
			if (!is_object(self::$_fileRoot)) {
				self::addRequiredRoots();
			}
		}
		return self::$_fileRoot;
	}

	/**
	 * Gets the package root node, if it doesn't exist it will be created
	 * @return ADocumentationHierarchy the package root node
	 */
	public static function getPackageRoot() {
		if (self::$_packageRoot === null) {
			self::$_packageRoot = self::model()->findByPk(self::PACKAGE_ROOT);
			if (!is_object(self::$_packageRoot)) {
				self::addRequiredRoots();
			}
		}
		return self::$_packageRoot;
	}

	/**
	 * Gets the interface root node, if it doesn't exist it will be created
	 * @return ADocumentationHierarchy the interface root node
	 */
	public static function getInterfaceRoot() {
		if (self::$_interfaceRoot === null) {
			self::$_interfaceRoot = self::model()->findByPk(self::INTERFACE_ROOT);
			if (!is_object(self::$_interfaceRoot)) {
				self::addRequiredRoots();
			}
		}
		return self::$_interfaceRoot;
	}

	/**
	 * Gets the class root node, if it doesn't exist it will be created
	 * @return ADocumentationHierarchy the class root node
	 */
	public static function getNamespaceRoot() {
		if (self::$_namespaceRoot === null) {
			self::$_namespaceRoot = self::model()->findByPk(self::NAMESPACE_ROOT);
			if (!is_object(self::$_namespaceRoot)) {
				self::addRequiredRoots();
			}
		}
		return self::$_namespaceRoot;
	}

	/**
	 * Adds the required root nodes
	 */
	public static function addRequiredRoots() {
		self::$_fileRoot = new ADocumentationHierarchy();
		self::$_fileRoot->id = self::FILE_ROOT;
		self::$_fileRoot->saveNode();

		self::$_packageRoot = new ADocumentationHierarchy();
		self::$_packageRoot->id = self::PACKAGE_ROOT;
		self::$_packageRoot->saveNode();

		self::$_interfaceRoot = new ADocumentationHierarchy();
		self::$_interfaceRoot->id = self::INTERFACE_ROOT;
		self::$_interfaceRoot->saveNode();

		self::$_namespaceRoot = new ADocumentationHierarchy();
		self::$_namespaceRoot->id = self::NAMESPACE_ROOT;
		self::$_namespaceRoot->saveNode();

	}
	/**
	 * Declares the behaviors to attach to this model
	 * @return array the behavior configuration
	 */
	public function behaviors() {
		return array(
			"ENestedSet" => array(
					"class" => "packages.nestedSet.ENestedSetBehavior",
					"hasManyRoots" => true,
					"rootAttribute" => "root",
					"leftAttribute" => "lft",
					"rightAttribute" => "rgt",
					"levelAttribute" => "level",
				),
		);
	}
	/**
	 *
	 * @return array the relation configuration
	 */
	public function relations() {
		return array(
			"entity" => array(self::BELONGS_TO,"ADocumentationEntity","entityId"),
		);
	}

	/**
	 * The table name for this model
	 * @return string the table name
	 */
	public function tableName() {
		return "documentationhierarchy";
	}
	/**
	 * Gets the database connection to use for this model.
	 * @return CDbConnection The database connection from the docs module
	 */
	public function getDbConnection() {
		return Yii::app()->db;
		#return Yii::app()->getModule("docs")->getDb();
		// TODO: Re-enable me
	}
}
