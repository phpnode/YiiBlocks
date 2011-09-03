<?php
/**
 * Manages authorisation roles
 * @author Charles Pick
 * @package packages.rbac.controllers
 */
class RoleController extends AAdminBaseController {
	/**
	 * Shows a list of roles
	 */
	public function actionIndex() {
		$model = new AAuthRole("search");
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['AAuthRole'])) {
			$model->attributes=$_GET['AAuthRole'];
		}
		$this->render("index",array("model" => $model));
	}
	/**
	 * Shows a particular role
	 * @param string $slug the slug of the role to show
	 */
	public function actionView($slug) {
		$model = $this->loadModel($slug);
		$this->render("view",array("model" => $model));
	}
	/**
	 * Creates a new role
	 */
	public function actionCreate() {
		$model = new AAuthRole();
		$this->performAjaxValidation($model);
		if (isset($_POST['AAuthRole'])) {
			$model->attributes = $_POST['AAuthRole'];
			if ($model->save()) {
				Yii::app()->user->setFlash("success","<h3>Role added successfully</h3>");
				$this->redirect(array("view","slug" => $model->slug));
			}
		}
		$this->render("create",array("model" => $model));
	}
	/**
	 * Updates a particular role
	 * @param string $slug the slug of the role to load
	 */
	public function actionUpdate($slug) {
		$model = $this->loadModel($slug);
		$this->performAjaxValidation($model);
		if (isset($_POST['AAuthRole'])) {
			$model->attributes = $_POST['AAuthRole'];
			if ($model->save()) {
				Yii::app()->user->setFlash("success","<h3>Role updated successfully</h3>");
				$this->redirect($model->createUrl());
			}
		}
		$this->render("update",array("model" => $model));
	}
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param string $slug the slug of the model to be deleted
	 */
	public function actionDelete($slug)
	{
		if(Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$this->loadModel($slug)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax'])) {
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
			}
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	/**
	 * Sets the operations for this role
	 * @param string $slug the slug of the role
	 */
	public function actionSetOperations($slug) {
		if (!Yii::app()->request->isPostRequest || !Yii::app()->request->isAjaxRequest) {
			throw new CHttpException(400,"Invalid Request");
		}
		$model = $this->loadModel($slug);
		$currentOperations = array();
		foreach($model->getChildren(AAuthItem::AUTH_OPERATION) as $op) {
			$currentOperations[$op->name] = $op->name;
		}
		if (isset($_POST['operations'])) {
			foreach($_POST['operations'] as $opName) {
				if (isset($currentOperations[$opName])) {
					unset($currentOperations[$opName]);
					continue;
				}
				Yii::app()->authManager->addItemChild($model->name,$opName);
			}
		}
		foreach($currentOperations as $opName) {
			Yii::app()->authManager->removeItemChild($model->name,$opName);
		}
	}

	/**
	 * Sets the tasks for this role
	 * @param string $slug the slug of the role
	 */
	public function actionSetTasks($slug) {
		if (!Yii::app()->request->isPostRequest || !Yii::app()->request->isAjaxRequest) {
			throw new CHttpException(400,"Invalid Request");
		}
		$model = $this->loadModel($slug);
		$currentTasks = array();
		foreach($model->getChildren(AAuthItem::AUTH_TASK) as $task) {
			$currentTasks[$task->name] = $task->name;
		}
		if (isset($_POST['tasks'])) {
			foreach($_POST['tasks'] as $taskName) {
				if (isset($currentTasks[$taskName])) {
					unset($currentTasks[$taskName]);
					continue;
				}
				Yii::app()->authManager->addItemChild($model->name,$taskName);
			}
		}
		foreach($currentTasks as $taskName) {
			Yii::app()->authManager->removeItemChild($model->name,$taskName);
		}
	}
	/**
	 * Loads a particular role
	 * @throws CHttpException if the model doesn't exist
	 * @param $slug the slug of the role to load
	 * @return AAuthRole
	 */
	public function loadModel($slug) {
		$model = AAuthRole::model()->findBySlug($slug);
		if (!is_object($model)) {
			throw new CHttpException(404, "No such role");
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param AAuthRole $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='aauth-role-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}