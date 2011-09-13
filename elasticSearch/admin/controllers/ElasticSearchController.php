<?php
/**
 * The default controller for the elastic search module
 *
 */
class ElasticSearchController extends ABaseAdminController {
	/**
	 * Shows the elastic search overview
	 */
	public function actionIndex() {
		$dataProvider = new CArrayDataProvider(
			array_values(Yii::app()->elasticSearch->indices->toArray()),
			array(
				'keyField' => 'name',
			)
		);
		$this->render("index",array("dataProvider" => $dataProvider));
	}
}