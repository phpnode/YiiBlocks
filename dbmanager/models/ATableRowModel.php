<?php
/**
 * Represents a row in a table
 * @package packages.dbmanager.models
 * @author Charles Pick
 */
class ATableRowModel extends CActiveRecord {
	/**
	 * Holds the table name
	 * @var string
	 */
	protected $_tableName;

	private $_md = array();								// meta data

	private static $_models=array();			// class name => model

	public static $_lastTableName;
	/**
	 * Constructor.
	 * @param string $scenario scenario name. See {@link CModel::scenario} for more details about this parameter.
	 * @param string $tableName the table name to use
	 */
	public function __construct($scenario='insert', $tableName = null) {
		if ($tableName !== null) {
			$this->setTableName($tableName);
		}
		if ($scenario === null) {
			return;
		}

		parent::__construct($scenario);
	}

	/**
	 * Returns the name of the associated database table.
	 * @see CActiveRecord::tableName()
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return $this->getTableName();
	}

	/**
	 * Creates an active record instance.
	 * This method is called by {@link populateRecord} and {@link populateRecords}.
	 * You may override this method if the instance being created
	 * depends the attributes that are to be populated to the record.
	 * For example, by creating a record based on the value of a column,
	 * you may implement the so-called single-table inheritance mapping.
	 * @param array $attributes list of attribute values for the active records.
	 * @return CActiveRecord the active record
	 * @since 1.0.2
	 */
	protected function instantiate($attributes)
	{
		$class=get_class($this);
		$model=new $class(null,$this->tableName());
		return $model;
	}

	/**
	 * Sets the table name for this instance
	 * @param string $tableName the table name
	 */
	public function setTableName($tableName)
	{
		$this->_tableName = $tableName;
		self::$_lastTableName = $tableName;
	}

	/**
	 * Gets the table name for this instance
	 * @return string the table name
	 */
	public function getTableName()
	{
		if ($this->_tableName === null) {
			return self::$_lastTableName;
		}
		return $this->_tableName;
	}

	/**
	 * Returns the meta-data for this AR
	 * @return CActiveRecordMetaData the meta for this AR class.
	 */
	public function getMetaData()
	{
		if(isset($this->_md[$this->getTableName()])) {
			return $this->_md[$this->getTableName()];
		}
		else {
			return $this->_md[$this->getTableName()]= new CActiveRecordMetaData($this);
		}
	}
	/**
	 * Gets the validation rules for the model
	 * @return array the validation rules for the model
	 */
	public function rules() {
		return array(
					array(implode(",",array_keys($this->getTableSchema()->columns)),"safe")
				);
	}

	/**
	 * Performs a search on the table
	 * @return CActiveDataProvider the data provider with the conditions applied
	 */
	public function search() {
		$criteria=new CDbCriteria;
		foreach($this->getMetaData()->columns as $name => $column) {
			if ($column->type == "string" && !stristr($column->dbType,"unsigned")) {
				$criteria->compare($name,$this->{$name},true);
			}
			else {
				$criteria->compare($name,$this->{$name});
			}
		}


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	/**
	 * Gets the columns to show in a grid view
	 * @return array the column configuration
	 */
	public function getGridColumns() {
		$columns = array();
		$fks = $this->getTableSchema()->foreignKeys;
		foreach($this->getTableSchema()->columns as $name => $column) {
			$item = array(
				"name" => $name,
			);
			if ($column->isForeignKey) {
				$item['value'] = 'CHtml::link($data->'.$name.',
									array("/admin/dbmanager/table/view",
							   			"name" => "'.$fks[$name][0].'",
							   			"'.get_class($this).'" => array("'.$fks[$name][1].'" => $data->'.$name.')
							   			))';
				$item['type'] = "raw";
			}
			$columns[] = $item;
		}
		return $columns;
	}
}