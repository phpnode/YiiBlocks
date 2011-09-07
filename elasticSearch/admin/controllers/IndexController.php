<?php
/**
 * Manages elastic search indexes
 * @author Charles Pick
 * @package packages.elasticSearch.admin.controllers
 */
class IndexController extends ABaseAdminController {
	/**
	 * Shows a list of elastic search indexes
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
	/**
	 * Displays a particular elastic search index
	 * @param string $name the name of the elastic search index
	 * @param string $type the type to show, if null the first type will be shown
	 * @param string $q the search query, if any
	 */
	public function actionView($name, $type = null, $q = null) {
		$model = $this->loadModel($name);
		if ($type === null) {
			$type = array_shift($model->getTypes()->toArray());
		}
		else {
			$type = $model->getTypes()->{$type};
		}
		if ($q) {
			$type->dataProvider->criteria->query()->text()->_all = $q;
		}
		$this->render("view",array("model" => $model, "type" => $type));
	}

	/**
	 * Loads an elastic search index by name
	 * @throws CHttpException if the index doesn't exist
	 * @param string $name the index name
	 * @return AElasticSearchIndex the loaded index
	 */
	protected function loadModel($name) {
		if (!isset(Yii::app()->elasticSearch->indices->{$name})) {
			throw new CHttpException(404, "No such index");
		}
		return Yii::app()->elasticSearch->indices->{$name};
	}
}