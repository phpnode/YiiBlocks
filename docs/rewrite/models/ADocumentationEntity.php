<?php
/**
 * A base class for documentation models.
 * Documentation models are used by the documentation generator to store information
 * about a particular entity.
 * @author Charles Pick
 * @package packages.docs.models
 *
 * @property integer $id the id of the entity
 * @property string $modelClass the class name of the model to instantiate
 * @property string $name the name of the entity
 * @property string $value a string representation of the value for this item
 * @property string $introduction the introduction for this entity
 * @property string $description a description of this entity
 * @property string $rawComment the raw comment for this entity
 * @property integer $timeAdded the time this entity was added
 *
 * @property ADocumentationHierarchy $hierarchy the entity's position in the documentation hierarchy
 */
class ADocumentationEntity extends CActiveRecord {
	/**
	 * The entity instances
	 * @var array
	 * @see loadEntity()
	 */
	protected static $_instances = array();
	/**
	 * Holds the children for this entity
	 * @var ADocumentationEntity[]
	 */
	protected $_children;

	/**
	 * Returns the static model instance
	 * @param string $className the name of the model class
	 * @return ADocumentationEntity the model instance
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * Instantiates the appropriate model
	 * @param array $attributes
	 * @return ADocumentationEntity
	 */
	protected function instantiate($attributes) {
		if (!isset($attributes['modelClass']) || $attributes['modelClass'] == "") {
			$class = "ADocumentationEntity";
		}
		else {
			$class=$attributes['modelClass'];
		}
		$model=new $class(null);
		return $model;
	}


	public function init() {
		$this->setTableAlias(get_class($this));
	}

	/**
	 * The table name for this model
	 * @return string the table name
	 */
	public function tableName() {
		return "documentationentities";
	}


	/**
	 * @return array
	 */
	public function defaultScope() {
		$scope = array(
			"with" => "hierarchy",

		);
		$class = get_class($this);
		if ($class !== __CLASS__) {
			$scope["condition"] = $this->getTableAlias(false, false).".modelClass = :modelClass";
			$scope["params"] = array(
				":modelClass" => $class,
			);
		}
		return $scope;
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
	/**
	 * Gets the primary root to use for this entity
	 * @return ADocumentationHierarchy the primary root node
	 */
	public function getRoot() {
		return ADocumentationHierarchy::getNamespaceRoot();
	}
	/**
	 * Declares the relations for this model
	 * @return array the relation configuration
	 */
	public function relations() {
		return array(
			"hierarchy" => array(
				self::HAS_ONE,
				"ADocumentationHierarchy",
				"entityId",
				"condition" => "hierarchy.root = :root",
				"order" => "hierarchy.level ASC",
				"params" => array(
					":root" => $this->getRoot()->root,
				)
			),
			"references" => array(
				self::HAS_MANY,
				"ADocumentationHierarchy",
				"entityId"
			)
		);
	}
	/**
	 * Gets the children for this entity
	 * @return ADocumentationEntity[]
	 */
	public function getChildren() {
		if ($this->_children === null) {
			$this->_children = array();
			foreach($this->hierarchy->children()->with("entity")->together()->findAll() as $child) {
				$this->_children[] = $child->entity;
			}
		}
		return $this->_children;
	}
	/**
	 * Appends a child to this entity
	 * @param ADocumentationEntity $entity the child entity to add
	 * @param boolean $runValidation whether to run validation on the model or not
	 * @return boolean true if the child was added
	 */
	public function addChild(ADocumentationEntity $entity, $runValidation = true) {
		// first ensure we don't already have a child with this class and name
		foreach($this->getChildren() as $child) {
			if ($child->modelClass == $entity->modelClass && $child->name == $entity->name) {
				return false;
			}
		}
		if (!$entity->save($runValidation)) {
			return false;
		}

		$item = $entity->hierarchy;
		if (!$item->moveAsLast($this->hierarchy)) {
			return false;
		}
		$this->_children[] = $entity;
		return true;
	}
	/**
	 * Triggered before the model saves, sets the correct model class and the time added
	 * @return boolean whether the save should continue
	 */
	public function beforeSave() {
		$this->modelClass = get_class($this);
		if ($this->getIsNewRecord()) {
			$this->timeAdded = (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
		}
		return parent::beforeSave();
	}
	/**
	 * Triggered after the model saves
	 * Adds an item to the hierarchy if required
	 */
	public function afterSave() {
		if ($this->getIsNewRecord()) {
			$hierarchy = new ADocumentationHierarchy();
			$hierarchy->entityId = $this->id;
			$hierarchy->appendTo($this->getRoot());
		}
		parent::afterSave();
	}
	/**
	 * Cleans up the hierarchy after the model is deleted
	 */
	public function afterDelete() {
		parent::afterDelete();
		foreach($this->getChildren() as $child) {
			$child->delete();
		}
		$this->hierarchy->deleteNode();
	}
	/**
	 * Named Scope: Finds an entity based on the given name.
	 * The name can be qualified using backslashes
	 * @param $name
	 * @return ADocumentationEntity $this with the scope applied
	 */
	public function byName($name) {
		if (strstr($name,"\\")) {
			$parts = preg_split("#\\\#",$name,null,PREG_SPLIT_NO_EMPTY);
			$select = array();
			$from = array();
			$where = array();
			$totalParts = count($parts);
			$params = array();
			$joins = array();
			$previous = null;
			$criteria = new CDbCriteria;
			$mainTableAlias = $this->getTableAlias();
			$tableName = $this->tableName();
			$hierarchyTableName = ADocumentationHierarchy::model()->tableName();
			foreach($parts as $n => $part) {
				$tableAlias = "item_".$n;
				if ($n == ($totalParts - 1)) {
					$tableAlias = $mainTableAlias;
					$select[] = $tableAlias.".*";
					$from[] = $tableName." ".$tableAlias;
					#$where[] = $tableAlias.".modelClass = :".$tableAlias."_modelClass";
					#$params[":".$tableAlias."_modelClass"] = get_class($this);
				}
				else {
					$joins[] = "INNER JOIN ".$tableName." ".$tableAlias;
				}
				$joins[] = "INNER JOIN ".$hierarchyTableName." ".$tableAlias."_h ON ".$tableAlias."_h.entityId = ".$tableAlias.".id";
				$where[] = $tableAlias.".name = :".$tableAlias."_name";
				$params[":".$tableAlias."_name"] = $part;
				if ($previous !== null) {
					$where[] = $tableAlias."_h.lft > ".$previous."_h.lft";
					$where[] = $tableAlias."_h.rgt < ".$previous."_h.rgt";
				}
				$where[] = $tableAlias."_h.level = ".($n + 2);
				$previous = $tableAlias;

			}
			$criteria->join = implode("\n",$joins);
			$criteria->condition = implode(" AND ",$where);
			$criteria->params = $params;

			#$sql = "SELECT ".implode(", ",$select)." FROM (".implode(", ",$from).") ".implode(" ",$joins)." WHERE ".implode(" AND ",$where)." LIMIT 1";
			#print_r("\n".strtr($sql,array_map(function($a) { return "'".$a."'"; },$params))."\n");
			#return self::model()->findBySql($sql,$params);
		}
		else {
			$mainTableAlias = $this->getTableAlias();
			$criteria = new CDbCriteria();
			$criteria->condition = $mainTableAlias.".name = :".$mainTableAlias."_name";
			$criteria->params[":".$mainTableAlias."_name"] = $name;
		}
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
		#return self::model()->findByAttributes(array("name" => $name, "modelClass" => get_class($this)));
	}
	/**
	 * Loads an entity
	 * @param string $name the name of the entity to load
	 * @param boolean $createIfEmpty whether to create the entity if it doesn't exist
	 * @return ADocumentationEntity|false the loaded model
	 */
	public function load($name, $createIfEmpty = false) {
		$className = get_class($this);
		if (isset(self::$_instances[$className][$name])) {
			return self::$_instances[$className][$name];
		}
		$entity = $this->byName($name)->find();
		if (is_object($entity)) {
			if (!isset(self::$_instances[$className])) {
				self::$_instances[$className] = array();
			}
			self::$_instances[$className][$name] = $entity;
			return $entity;
		}
		if (!$createIfEmpty) {
			return false;
		}
		if (strstr($name,"\\")) {
			// namespaced
			$parts = preg_split("#\\\#",$name,null,PREG_SPLIT_NO_EMPTY);
			$entityName = array_pop($parts);
			$parent = null;
			foreach($parts as $n => $part) {
				if ($parent === null) {
					$parent = ADocumentationNamespaceEntity::model()->load($part,true);
				}
				else {
					$item = $parent->children()->byName($part)->find();
					if (!is_object($item)) {
						$item = new ADocumentationNamespaceEntity();
						$item->name = $part;
						$parent->addChild($item);
					}
					$parent = $item;
				}
			}
			if ($parent === null) {
				return $this->load($entityName,true);
			}
			$entity = new $className;
			$entity->name = $entityName;
			$parent->addChild($entity);
		}
		else {
			$entity = new $className;
			$entity->name = $name;
			$entity->save();
		}
		if (!isset(self::$_instances[$className])) {
			self::$_instances[$className] = array();
		}
		self::$_instances[$className][$name] = $entity;
		return $entity;
	}
}
