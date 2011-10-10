<?php
/**
 * Provides a base class for RESTful controllers
 *
 * @author Charles Pick
 * @package packages.services
 */
abstract class ARestController extends Controller {
	/**
	 * The default format to use.
	 * Can be "xml", "json" or "jsonp"
	 * @var string
	 */
	public $format = "json";

	/**
	 * The class name of the model this controller primarily uses
	 * @var string
	 */
	public $modelClass;

	/**
	 * Gets the static model associated with this controller.
	 * This does not load a specific model instance, it returns the static model.
	 * @throws CException if no model is set for this class
	 * @return CActiveRecord The static model instance
	 */
	public function getModel() {
		if ($this->modelClass === null) {
			throw new CException("No model is specified for this controller");
		}
		$modelClass = $this->modelClass;
		return $modelClass::model();
	}
	/**
	 * Loads a specific model
	 * @throws CHttpException if the model doesn't exist
	 * @param mixed $pk The primary key of the model to load
	 * @return CActiveRecord the loaded model
	 */
	public function loadModel($pk) {
		$model = $this->getModel()->findByPk($pk);
		if (!is_object($model)) {
			throw new CHttpException(404, "The specified item cannot be found.");
		}
		return $model;
	}

	/**
	 * Shows a list of models of a particular type
	 */
	public function actionIndex() {
		throw new CHttpException(404,"This operation is not implemented");
	}
	/**
	 * Displays a model of a particular type
	 */
	public function actionView() {
		throw new CHttpException(404,"This operation is not implemented");
	}
	/**
	 * Creates a model of a particular type
	 */
	public function actionCreate() {
		throw new CHttpException(404,"This operation is not implemented");
	}

	/**
	 * Updates a specific model
	 */
	public function actionUpdate() {
		throw new CHttpException(404,"This operation is not implemented");
	}

	/**
	 * Deletes a specific model
	 */
	public function actionDelete() {
		throw new CHttpException(404,"This operation is not implemented");
	}

	/**
	 * Renders the data and sends it to the client
	 * @param $data the data to render
	 */
	public function render($data) {
		switch($this->format) {
			case "jsonp":
				$response = new AJSONResponse($data);
				$response->setJSONP(true);
				break;
			case "xml":
				$response = new AXMLResponse($data);
				break;
			case "json": // fall through, json is the default
			default:
				$response = new AJSONResponse($data);
				break;
		}
		$response->render();
	}
}