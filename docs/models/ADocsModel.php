<?php
/**
 * A base class for documentation models.
 * Documentation models are used by the documentation generator to store information
 * about a particular entity.
 * @author Charles Pick
 * @package packages.docs.models
 */
abstract class ADocsModel extends CActiveRecord {
	/**
	 * Gets the database connection to use for this model.
	 * @return CDbConnection The database connection from the docs module
	 */
	public function getDbConnection() {
		return Yii::app()->getModule("docs")->getDb();
	}
}
