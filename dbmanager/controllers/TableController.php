<?php
/**
 * Provides functions for managing database tables
 * @author Charles Pick
 * @package packages.dbmanager.controllers
 */
class TableController extends ABaseAdminController {
	/**
	 * Shows a list of tables in the database
	 */
	public function actionIndex() {
		$tables = array();
		foreach(Yii::app()->db->getSchema()->getTableNames() as $tableName) {
			$model = new ATableModel();
			$model->name = $tableName;
			$tables[] = $model;
		}
		$dataProvider = new CArrayDataProvider($tables,
						array(
							"keyField" => "name",
							"pagination" => false,
							"sort" => false,
						));
		$this->render("index",array("dataProvider" => $dataProvider));
	}
	/**
	 * Views a particular table
	 * @param string $name the table name
	 */
	public function actionView($name) {
		$model = $this->loadModel($name);
		$rowFilter = new ATableRowModel('search',$name);
		$rowFilter->unsetAttributes(); // clear default values
		if (isset($_GET['ATableRowModel'])) {
			$rowFilter->attributes = $_GET['ATableRowModel'];
		}
		$this->render("view",array("model" => $model, "rowFilter" => $rowFilter));
	}
	/**
	 * Loads a table model with the given name
	 * @throws CHttpException if the table doesn't exist
	 * @param string $name the table name
	 * @return ATableModel the loaded model
	 */
	protected  function loadModel($name) {
		$schema = Yii::app()->db->getSchema()->getTable($name);
		if (!is_object($schema)) {
			throw new CHttpException(404, "No such table");
		}
		$model = new ATableModel();
		$model->name = $name;
		$model->setSchema($schema);
		return $model;
	}
}