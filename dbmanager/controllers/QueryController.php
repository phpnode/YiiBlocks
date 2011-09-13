<?php
/**
 * Allows the administrator to execute arbitary SQL queries
 * @author Charles Pick
 * @package packages.dbmanager.controllers
 */
class QueryController extends ABaseAdminController {
	/**
	 * The default action
	 * @var string
	 */
	public $defaultAction = "execute";

	/**
	 * Executes a query
	 */
	public function actionExecute() {
		$model = new ASqlQueryForm();
		$dataProvider = false;
		if (isset($_POST['ASqlQueryForm'])) {
			$model->attributes = $_POST['ASqlQueryForm'];
			if ($model->validate()) {
				$totalItems = 0;
				if ($model->getIsSelectQuery()) {
					$command = Yii::app()->db->createCommand("SELECT COUNT(*) FROM ($model->sql) AS blah");
					$totalItems = $command->queryScalar();
				}

				$dataProvider = new CSqlDataProvider($model->sql,
								array(
									"totalItemCount" => $totalItems,
									"pagination" => array(
										"pageSize" => 30
									)
								));
			}
		}
		$this->render("execute",array("model" => $model, "dataProvider" => $dataProvider));
	}
}