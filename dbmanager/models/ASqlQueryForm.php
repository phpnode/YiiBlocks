<?php
/**
 * A form model for user supplied SQL queries
 * @author Charles Pick
 * @package packages.dbmanager.models
 */
class ASqlQueryForm extends CFormModel {
	/**
	 * The SQL command
	 * @var string
	 */
	public $sql;

	public function getIsSelectQuery() {
		return (substr(strtolower(trim($this->sql)),0,7) == "select ");
	}
	/**
	 * Gets the validation rules for this model
	 * @return array the validation rules
	 */
	public function rules() {
		return array(
			array("sql","required"),
		);
	}
}