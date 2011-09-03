<?php
/**
 * Manages authorisation operations
 * @author Charles Pick
 * @package packages.rbac.controllers
 */
class OperationController extends AAdminBaseController {
	/**
	 * Shows a list of operations
	 */
	public function actionIndex() {
		$model = new AAuthOperation("search");
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['AAuthOperation'])) {
			$model->attributes=$_GET['AAuthOperation'];
		}
		$this->render("index",array("model" => $model));
	}
	/**
	 * Shows a particular operation
	 * @param $slug the slug of the operation to show
	 */
	public function actionView($slug) {
		$model = $this->loadModel($slug);
		$this->render("view",array("model" => $model));
	}
	/**
	 * Creates a new operation
	 * @param string $assignTo The slug of the auth item to assign this to after saving, if any.
	 */
	public function actionCreate($assignTo = null) {
		$model = new AAuthOperation();
		$this->performAjaxValidation($model);
		if (isset($_POST['AAuthOperation'])) {
			$model->attributes = $_POST['AAuthOperation'];
			if ($model->save()) {
				Yii::app()->user->setFlash("success","<h3>Operation added successfully</h3>");
				if ($assignTo !== null) {
					$parent = AAuthItem::model()->findBySlug($assignTo);
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
	 * Updates a particular operation
	 * @param string $slug The slug of the operation to load
	 */
	public function actionUpdate($slug) {
		$model = $this->loadModel($slug);
		$this->performAjaxValidation($model);
		if (isset($_POST['AAuthOperation'])) {
			$model->attributes = $_POST['AAuthOperation'];
			if ($model->save()) {
				Yii::app()->user->setFlash("success","<h3>Operation updated successfully</h3>");
				$this->redirect(array("view","slug" => $model->slug));
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
	 * Tries to find the controller for the given route
	 */
	public function actionFindRoute() {
		$comment = "";
		$route = $_POST['AAuthOperation']['name'];
		list($controller,$actionId)  = Yii::app()->createController($route);
		if (!is_object($controller)) {
			// no such controller
			if (substr($route,-2,2) == "/*") {
				$route = substr($route,0,-2);
				list($controller,$actionId)  = Yii::app()->createController($route);
				if (is_object($controller) && is_object($controller->module)) {
					$reflection = new ReflectionObject($controller->module);
					$comment=strtr(trim(preg_replace('/^\s*\**( |\t)?/m','',trim(trim($reflection->getDocComment()),'/'))),"\r",'');
					if(preg_match('/^\s*@\w+/m',$comment,$matches,PREG_OFFSET_CAPTURE)) {
						$comment=trim(substr($comment,0,$matches[0][1]));
					}
				}
			}
		}
		else {
			if ($actionId == "") {
				$actionId = $controller->defaultAction;
			}
			$reflection = new ReflectionObject($controller);
			if ($actionId == "*") {
				$comment=strtr(trim(preg_replace('/^\s*\**( |\t)?/m','',trim(trim($reflection->getDocComment()),'/'))),"\r",'');
				if(preg_match('/^\s*@\w+/m',$comment,$matches,PREG_OFFSET_CAPTURE)) {
					$comment=trim(substr($comment,0,$matches[0][1]));
				}
			}
			else {
				if ($reflection->hasMethod("action".$actionId)) {
					$action = $reflection->getMethod("action".$actionId);
					$comment=strtr(trim(preg_replace('/^\s*\**( |\t)?/m','',trim(trim($action->getDocComment()),'/'))),"\r",'');
					if(preg_match('/^\s*@\w+/m',$comment,$matches,PREG_OFFSET_CAPTURE)) {
						$comment=trim(substr($comment,0,$matches[0][1]));
					}
				}
			}
		}
		$response = array(
			"comment" => $comment,
			"url" => $this->createAbsoluteUrl($route),
		);
		header("Content-type: application/json");
		echo json_encode($response);
	}
	/**
	 * Loads a particular operation
	 * @throws CHttpException if the model doesn't exist
	 * @param $slug the slug of the operation to load
	 * @return AAuthOperation
	 */
	public function loadModel($slug) {
		$model = AAuthOperation::model()->findBySlug($slug);
		if (!is_object($model)) {
			throw new CHttpException(404, "No such operation");
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param AAuthOperation $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='aauth-operation-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}