<?php
/**
 * Documentation generator
 */
class DocsModule extends CWebModule {
	/**
	 * Holds the database connection
	 * @var CDbConnection
	 */
	protected $_db;
	
	/**
	 * Gets the docs database connection
	 * @return CDbConnection
	 */
	public function getDb() {
		if ($this->_db === null) {
			$this->_db = new CDbConnection("sqlite:".Yii::getPathOfAlias("packages.docs")."/docs.db");
		}
		return $this->_db;
	}
}
