<?php
/**
 * Manages authorisation tasks
 * @author Charles Pick
 * @package packages.rbac.controllers
 */
class TaskController extends ABaseAdminController {

	public function filters() {
		return array(
			array("packages.rbac.components.ARbacFilter")
		);
	}
	/**
	 * Shows a list of tasks
	 */
	public function actionIndex() {
		$model = new AAuthTask("search");
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['AAuthTask'])) {
			$model->attributes=$_GET['AAuthTask'];
		}
		$this->render("index",array("model" => $model));
	}
	/**
	 * Shows a particular task
	 * @param string $slug the name of the task to show
	 */
	public function actionView($slug) {
		$model = $this->loadModel($slug);
		$this->render("view",array("model" => $model));
	}
	/**
	 * Creates a new task
	 * @param string $assignTo The name of the auth item to assign this to after saving, if any.
	 */
	public function actionCreate($assignTo = null) {
		$model = new AAuthTask();
		$this->performAjaxValidation($model);
		if (isset($_POST['AAuthTask'])) {
			$model->attributes = $_POST['AAuthTask'];
			if ($model->save()) {
				Yii::app()->user->setFlash("success","<h3>Task added successfully</h3>");
				if ($assignTo !== null) {
					$parent = AAuthItem::model()->findByPk($assignTo);
					if (is_object($parent)) {
						Yii::app()->authManager->addItemChild($parent->name,$model->name);
						switch(get_class($parent)) {
							case "AAuthOperation":
								$this->redirect(array("operation/view","slug" => $parent->slug));
								break;
							case "AAuthTask":
								$this->redirect(array("task/view","slug" => $parent->slug));
								break;
							case "AAuthRole":
								$this->redirect(array("role/view","slug" => $parent->slug));
								break;
						}
					}
				}
				$this->redirect(array("view","slug" => $model->slug));
			}
		}
		$this->render("create",array("model" => $model));
	}
	/**
	 * Updates a particular task
	 * @param string $slug The slug of the task to load
	 */
	public function actionUpdate($slug) {
		$model = $this->loadModel($slug);
		$this->performAjaxValidation($model);
		if (isset($_POST['AAuthTask'])) {
			$model->attributes = $_POST['AAuthTask'];
			if ($model->save()) {
				Yii::app()->user->setFlash("success","<h3>Task updated successfully</h3>");
				$this->redirect(array("view","slug" => $model->slug));
			}
		}
		$this->render("update",array("model" => $model));
	}
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param string $slug the ID of the model to be deleted
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
	 * Sets the operations for this task
	 * @param string $slug the name of the operation
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
	 * Loads a particular task
	 * @throws CHttpException if the model doesn't exist
	 * @param $slug the name of the task to load
	 * @return AAuthTask
	 */
	public function loadModel($slug) {
		$model = AAuthTask::model()->findBySlug($slug);
		if (!is_object($model)) {
			throw new CHttpException(404, "No such task");
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param AAuthTask $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='aauth-task-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}