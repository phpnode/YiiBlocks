<?php
/**
 * Represents a table in the database
 * @author Charles Pick
 * @package packages.dbmanager.models
 */
class ATableModel extends CFormModel {
	/**
	 * The table name
	 * @var string
	 */
	public $name;
	/**
	 * The total number of rows in the table
	 * @var integer
	 */
	protected $_totalRows;

	/**
	 * The table schema
	 * @var CDbTableSchema
	 */
	protected $_schema;

	/**
	 * The data provider for the table
	 * @var CArrayDataProvider
	 */
	protected $_dataProvider;
	/**
	 * Sets the table schema
	 * @param CDbTableSchema $schema the table schema
	 */
	public function setSchema($schema) {
		$this->_schema = $schema;
	}

	/**
	 * Gets the schema for the table
	 * @return CDbTableSchema
	 */
	public function getSchema() {
		if ($this->_schema === null) {
			$this->_schema = Yii::app()->db->getSchema()->getTable($this->name);
		}
		return $this->_schema;
	}

	/**
	 * Sets the total rows for this table
	 * @param integer $totalRows
	 */
	public function setTotalRows($totalRows) {
		$this->_totalRows = $totalRows;
	}

	/**
	 * Gets the total rows for this table
	 * @return integer the total number of rows in this table
	 */
	public function getTotalRows() {
		if ($this->_totalRows === null) {
			$this->_totalRows = Yii::app()->db->createCommand("SELECT COUNT(*) FROM ".$this->name)->queryScalar();
		}
		return $this->_totalRows;
	}

	/**
	 * Sets the data provider for this table
	 * @param CArrayDataProvider $dataProvider the data provider for this table
	 */
	public function setDataProvider($dataProvider) {
		$this->_dataProvider = $dataProvider;
	}

	/**
	 * Gets the data provider for this table
	 * @return CArrayDataProvider the data provider for this table
	 */
	public function getDataProvider() {
		if ($this->_dataProvider === null) {
			$model = new ATableRowModel("update",$this->name);

			$this->_dataProvider = new CActiveDataProvider($model);

		}
		return $this->_dataProvider;
	}
}