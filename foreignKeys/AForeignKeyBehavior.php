<?php
/**
 * Provides foreign key functionality for databases / schemas that don't support FKs natively.
 * 
 * <pre>
 * $user = User::model()->findByPk(1);
 * $fkBehavior = new AForeignKeyBehavior;
 * $fkBehavior->mapping = array(
 * 	array(
 *		"attribute" => "id", // the attribute on the user model that this fk refers to
 * 		"foreignModel" => "Preference", // the class of the model that depends on this
 * 		"foreignAttribute" => "userId", // the attribute in the preference model that refers to this attribute
 * 		"delete" => "cascade", // when a user is deleted, also delete the associated preferences
 * 		"update" => "cascade", // when a user id changes, update the userId field in the preferences table
 * 	),
 * 	array(
 * 		"attribute" => "id",
 * 		"foreignModel" => "Invoice",
 * 		"foreignAttribute" => "userId",
 * 		"delete" => "restrict", // don't allow users to be deleted if there are associated invoices
 * 		"update" => "cascade",
 * 	)
 * );
 * $user->attachBehavior("AForeignKeyBehavior",$fkBehavior);
 * </pre>
 * @author Charles Pick
 * @package packages.foreignKeys 
 */
class AForeignKeyBehavior extends CActiveRecordBehavior {
	/**
	 * The mapping for the foreign keys.
	 * @see getMapping()
	 * @see setMapping()
	 * @var AForeignKeyMap[]
	 */
	protected $_mapping = array();
	
	/**
	 * Gets the foreign key mappings for this model
	 * @return AForeignKeyMap[] the fk mappings for this model
	 */
	public function getMapping() {
		return $this->_mapping;
	}
	
	/**
	 * Sets the foreign key mappings for this model
	 * @param array $mapping An array of mappings for this model, either instances of AForeignKeyMap or an array of configuration items for AForeignKeyMap 
	 * @return AForeignKeyMap[] the fk mappings for this model
	 */
	public function setMapping($mapping) {
		$this->_mapping = array();
		foreach($mapping as $map) {
			if (is_array($map)) {
				$map['owner'] = $this;
				$map = new AForeignKeyMap($map);
			}
			else {
				$map->owner = $this;
			}
			$this->_mapping[] = $map;
		}
		return $this->_mapping;
	}
	
	/**
	 * Ensures that the foreign keys are valid before the owner model is saved.
	 * @param CModelEvent $event the beforeSave event
	 */
	public function beforeSave(CModelEvent $event) {
		if ($event->sender->isNewRecord) {
			// we can't perform any foreign key actions on a new record
			return true;
		}
		// this is an update, let's see if any of the foreign attributes have changed
		foreach($this->mapping as $map) {
			if ($this->owner->{$map->attribute} == $map->value) {
				continue;
			}
			// this attribute has changed
			switch (strtolower($map->update)) {
				case "cascade":
					$criteria = new CDbCriteria;
					$criteria->condition = $map->foreignAttribute." = :value";
					$criteria->params[":value"] = $this->owner->{$map->attribute};
					foreach($map->foreignModel->findAll($criteria) as $dependent) {
						$dependent->{$map->foreignAttribute} = $this->owner->{$map->attribute};
						if (!$dependent->update(array($map->foreignAttribute))) {
							$event->isValid = false;
							$this->owner->addError($map->attribute, Yii::t("Foreign Key constraint failed: {foreignModel}.{foreignAttribute}",array("{foreignModel}" => $map->foreignModelClass, "{foreignAttribute}" => $map->foreignAttribute)));
						}
					}
					break;
				case "setnull":
					$criteria = new CDbCriteria;
					$criteria->condition = $map->foreignAttribute." = :value";
					$criteria->params[":value"] = $this->owner->{$map->attribute};
					foreach($map->foreignModel->findAll($criteria) as $dependent) {
						$dependent->{$map->foreignAttribute} = null;
						if (!$dependent->update(array($map->foreignAttribute))) {
							$event->isValid = false;
							$this->owner->addError($map->attribute, Yii::t("Foreign Key constraint failed: {foreignModel}.{foreignAttribute}",array("{foreignModel}" => $map->foreignModelClass, "{foreignAttribute}" => $map->foreignAttribute)));
						}
					}
					break;
				case "restrict":
					$criteria = new CDbCriteria;
					$criteria->condition = $map->foreignAttribute." = :value";
					$criteria->params[":value"] = $this->owner->{$map->attribute};
					if ($map->foreignModel->exists($criteria)) {
						$event->isValid = false;
						$this->owner->addError($map->attribute, Yii::t("Foreign Key constraint failed: {foreignModel}.{foreignAttribute}",array("{foreignModel}" => $map->foreignModelClass, "{foreignAttribute}" => $map->foreignAttribute)));
					}	
					break;
			}
		}
	}
	
	/**
	 * Ensures that the foreign keys are valid before the owner model is deleted.
	 * @param CModelEvent $event the beforeDelete event
	 */
	public function beforeDelete(CModelEvent $event) {
		foreach($this->mapping as $map) {
			switch (strtolower($map->update)) {
				case "cascade":
					$criteria = new CDbCriteria;
					$criteria->condition = $map->foreignAttribute." = :value";
					$criteria->params[":value"] = $this->owner->{$map->attribute};
					foreach($map->foreignModel->findAll($criteria) as $dependent) {
						if (!$dependent->delete()) {
							$event->isValid = false;
							$this->owner->addError($map->attribute, Yii::t("Foreign Key constraint failed: {foreignModel}.{foreignAttribute}",array("{foreignModel}" => $map->foreignModelClass, "{foreignAttribute}" => $map->foreignAttribute)));
						}
					}
					break;
				case "setnull":
					$criteria = new CDbCriteria;
					$criteria->condition = $map->foreignAttribute." = NULL";
					foreach($map->foreignModel->findAll($criteria) as $dependent) {
						$dependent->{$map->foreignAttribute} = $this->owner->{$map->attribute};
						if (!$dependent->update(array($map->foreignAttribute))) {
							$event->isValid = false;
							$this->owner->addError($map->attribute, Yii::t("Foreign Key constraint failed: {foreignModel}.{foreignAttribute}",array("{foreignModel}" => $map->foreignModelClass, "{foreignAttribute}" => $map->foreignAttribute)));
						}
					}
					break;
				case "restrict":
					$criteria = new CDbCriteria;
					$criteria->condition = $map->foreignAttribute." = :value";
					$criteria->params[":value"] = $this->owner->{$map->attribute};
					if ($map->foreignModel->exists($criteria)) {
						$event->isValid = false;
						$this->owner->addError($map->attribute, Yii::t("Foreign Key constraint failed: {foreignModel}.{foreignAttribute}",array("{foreignModel}" => $map->foreignModelClass, "{foreignAttribute}" => $map->foreignAttribute)));
					}	
					break;
			}
		} 
	}
	
	/**
	 * The after find event
	 * @param CModelEvent $event the after find event
	 */
	public function afterFind(CModelEvent $event) {
		foreach($this->mapping as $map) {
			$map->value = $this->owner->{$map->attribute};
		}
	}
	
	
}

/**
 * Holds information about a foreign key mapping
 * @author Charles Pick
 * @package packages.foreignKeys
 */
class AForeignKeyMap extends CComponent {
	/**
	 * The model this foreign key belongs to.
	 * @var CActiveRecord
	 */
	public $owner;
	/**
	 * The attribute on the owner model that the foreign key points to
	 * @var string
	 */
	public $attribute;
	
	/**
	 * The value of the attribute, this will be used when checking if the attribute has changed
	 * @var mixed 
	 */
	public $value;
	
	/**
	 * The class name of the foreign model
	 * @var string
	 */
	public $foreignModelClass;
	
	/**
	 * The name of the attribute on the foreign model that points to the owner attribute.
	 * @var string
	 */
	public $foreignAttribute;
	/**
	 * The action to perform on delete.
	 * Either "cascade", "setnull","restrict" or null. If null no action will be performed
	 * @var string
	 */
	public $delete;
	
	/**
	 * The action to perform when the attribute on the owner model changes.
	 * Either "cascade", "setnull","restrict" or null. If null no action will be performed
	 * @var string
	 */
	public $update;
	
	/**
	 * Constructor, configures the fk
	 * @param array $config The configuration to apply to this model, key => value
	 */
	public function __construct($config = null) {
		if ($config !== null) {
			foreach($config as $attribute => $value) {
				$this->{$attribute} = $value;
			}
		}
	}
	/**
	 * Gets an instance of the foreign model
	 * @return CActiveRecord the foreign model
	 */
	public function getForiegnModel() {
		$modelClass = $this->foreignModelClass;
		return new $modelClass;
	}
}
