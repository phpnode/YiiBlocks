<?php
/**
 * Manages elastic search documents
 * @author Charles Pick
 * @package packages.elasticSearch.admin.controllers
 */
class DocumentController extends ABaseAdminController {
	/**
	 * Shows a list of elastic search documents
	 * @param string $index the name of the elastic search index
	 * @param string $type the document type
	 * @param string $q the search query, if any
	 */
	public function actionIndex($index, $type, $q = null) {
		if (!($index = Yii::app()->elasticSearch->indices->{$index})) {
			throw new CHttpException(404, "No such index");
		}

		if (!isset($index->types[$type])) {
			throw new CHttpException(404, "No such type");
		}
		$type = $index->types[$type];
		if ($q) {
			$type->dataProvider->criteria->query()->text()->_all = $q;
		}
		$dataProvider = $type->getDataProvider();
		$this->render("index",array("index" => $index, "type" => $type, "dataProvider" => $dataProvider));
	}
	/**
	 * Creates a new elastic search document
	 * @param string $index the name of the elastic search index
	 * @param string $type the document type
	 */
	public function actionCreate($index, $type) {
		if (isset(Yii::app()->elasticSearch->indices->{$index})) {
			$index = Yii::app()->elasticSearch->indices{$index};
		}
		else {
			$indexName = $index;
			$index = new AElasticSearchIndex();
			$index->name = $indexName;
			$index->connection = Yii::app()->elasticSearch;
		}
		if (isset($index->getTypes()->{$type})) {
			$type = $index->getTypes()->{$type};
		}
		else {
			$type = new AElasticSearchDocumentType($type,$index,$index->connection);
		}
		$document = new AElasticSearchDocument();
		$document->setType($type);
		if (isset($_POST[get_class($document)])) {
			$document->attributes = $_POST[get_class($document)];
			if ($document->save()) {
				Yii::app()->user->setFlash("success","<h2>Your changes were saved successfully</h2>");
				// we need to wait a second for ES to update
				sleep(1);
				$this->redirect(array("view", "index" => $index, "type" => $type, "id" => $document->getId()));
			}
		}
		$this->render("create",array("document" => $document));
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
		if(isset($_POST[get_class($document)])) {

			$document->attributes = $_POST[get_class($document)];
			if ($document->save()) {
				Yii::app()->user->setFlash("success","<h2>Your changes were saved successfully</h2>");
				// we need to wait a second for ES to update
				sleep(1);
				$this->redirect(array("view", "index" => $index, "type" => $type, "id" => $id));
			}
		}
		$this->render("update",array("document" => $document, "type" => $type));
	}

	/**
	 * Deletes a particular elastic search document
	 * @param mixed $id the id of the document to show
	 * @param string $index the name of the elastic search index
	 * @param string $type the document type
	 */
	public function actionDelete($index, $type, $id) {
		if(Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$this->loadDocument($index, $type, $id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax'])) {
				// we need to wait a second for ES to update
				sleep(1);
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index', "index" => $index, "type" => $type));
			}
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	/**
	 * Loads an elastic search document
	 * @throws CHttpException if the document doesn't exist
	 * @param mixed $id the id of the document to load
	 * @param string $index the name of the elastic search index
	 * @param string $type the document type
	 * @return AElasticSearchDocument the loaded index
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
		$criteria->query()->ids()->values(array($id));

		#die("<pre>".print_r($criteria->toArray(),true)."</pre>");

		if (($documents = $type->search($criteria)) === false || count($documents) == 0) {

			throw new CHttpException(404, "No such document");
		}
		return array_shift($documents->toArray());
	}
}