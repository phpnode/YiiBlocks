<?php
/**
 * Package controller handles displaying and publishing packages.
 * @author Charles Pick
 * @package packages.ypm.controllers
 */
class PackageController extends Controller {
	/**
	 * Declares class based actions
	 * @return array The class based actions to use for this controller
	 */
	public function actions() {
		return array(
			"browseFiles" => array(
				"class" => "packages.fileManager.actions.ABrowseDirectoryAction",
				"basePath" => array(
						Yii::getPathOfAlias("application"),
						Yii::getPathOfAlias("webroot"),
					)
			)
		);
	}
	/**
	 * Creates a new package
	 */
	public function actionCreate() {
		
		$model = new APackage("create");
		$this->performAjaxValidation($model);
		if (isset($_POST['APackage'])) {
			$model->attributes = $_POST['APackage'];
			if ($model->save()) {
				Yii::app()->user->setFlash("success",Yii::t("packages.ypm","<h2>Package Created</h2><p>Your new package was created successfully.</p>"));
				$this->redirect(array("/ypm/package/edit","name" => $model->name));
			}
		}
		$this->render("create",array("model" => $model));
	}
	
	/**
	 * Edits an installed package.
	 * Called edit rather than update to avoid confusion with updating packages.
	 * @param string $name The name of the package to edit
	 */
	public function actionEdit($name) {
		$model = Yii::app()->packageManager->packages[$name];
		if (!is_object($model)) {
			throw new CHttpException(404,Yii::t("packages.ypm","No such package"));
		}
		$this->performAjaxValidation($model);
		if (isset($_POST['APackage'])) {
			$model->attributes = $_POST['APackage'];
			if ($model->save()) {
				Yii::app()->user->setFlash("success",Yii::t("packages.ypm","<h2>Package Saved</h2><p>Your changes were saved successfully.</p>"));
				$this->refresh();
			}
		}
		$this->render("edit",array("model" => $model));
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
