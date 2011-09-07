<?php
/**
 * Manages elastic search documents
 * @author Charles Pick
 * @package packages.elasticSearch.admin.controllers
 */
class DocumentController extends ABaseAdminController {
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
	 * Displays a particular elastic search document
	 * @param mixed $id the id of the document to show
	 * @param string $index the name of the elastic search index
	 * @param string $type the document type
	 */
	public function actionView($index, $type, $id) {
		$document = $this->loadDocument($index, $type, $id);

		$this->render("view",array("document" => $document, "type" => $type));
	}
	/**
	 * Edits a particular elastic search document
	 * @param mixed $id the id of the document to show
	 * @param string $index the name of the elastic search index
	 * @param string $type the document type
	 */
	public function actionUpdate($index, $type, $id) {
		$document = $this->loadDocument($index, $type, $id);

		$this->render("update",array("document" => $document, "type" => $type));
	}
	/**
	 * Loads an elastic search document
	 * @throws CHttpException if the document doesn't exist
	 * @param mixed $id the id of the document to load
	 * @param string $index the name of the elastic search index
	 * @param string $type the document type
	 * @return AElasticSearchIndex the loaded index
	 */
	protected function loadDocument($index, $type, $id) {
		if (!($index = Yii::app()->elasticSearch->indices->{$index})) {
			throw new CHttpException(404, "No such index");
		}

		if (!isset($index->types[$type])) {
			throw new CHttpException(404, "No such type");
		}
		$type = $index->types[$type];
		$criteria = new AElasticSearchCriteria();
		$criteria->query()->ids()->values(array((int) $id));

		#die("<pre>".print_r($criteria->toArray(),true)."</pre>");

		if (($document = $type->search($criteria)) === false) {

			throw new CHttpException(404, "No such document");
		}
		return array_shift($document->toArray());
	}
}