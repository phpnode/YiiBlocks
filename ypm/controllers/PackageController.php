<?php
/**
 * Package controller handles displaying and publishing packages.
 * @author Charles Pick
 * @package packages.ypm.controllers
 */
class PackageController extends Controller {
	
	/**
	 * Creates a new package
	 */
	public function actionCreate() {
		if (isset($_POST['stage'])) {
			$stage = (int) $_POST['stage'];
		}
		else {
			$stage = 1;
		}
		$model = new APackage("stage".$stage);
		// load the persistant page state
		foreach($this->getPageState("APackage",array()) as $attribute => $value) {
			$model->{$attribute} = $value;
		}
		$this->performAjaxValidation($model);
		if (isset($_POST['APackage'])) {
			$model->attributes = $_POST['APackage'];
			if ($model->validate()) {
				$this->setPageState("APackage", $model->attributes);
				$stage = $stage + 1;
			}
		}
		$this->render("create",array("model" => $model, "stage" => $stage));
	}
	
	/**
	 * Shows a list of available packages in JSON format
	 * 
	 */
	public function actionList() {
		$response = new AJSONResponse();
		
		$response->packages = new CAttributeCollection();
		foreach(Yii::app()->packageManager->packages as $package) {
			if (!$package->isPublished) {
				continue;
			}
			$response->packages[$package->name] = $package;
		}
		
		$response->render();
		Yii::app()->packageManager->install("testItem1");
	}
	
	/**
	 * Performs the AJAX validation.
	 * @param CModel $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='package-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
