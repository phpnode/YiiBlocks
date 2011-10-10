<?php
/**
 * The UserController controller class deals with viewing and managing {@link User} models
 * @package application.controllers
 */
class UserController extends ABaseAdminController
{

	/**
	 * Displays a particular user.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$modelClass = Yii::app()->getModule("users")->userModelClass;
		$model=new $modelClass("admin");

		$this->performAjaxValidation($model);

		if(isset($_POST[$modelClass]))
		{
			$model->attributes=$_POST[$modelClass];
			if($model->save()) {
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$modelClass = Yii::app()->getModule("users")->userModelClass;
		$model=$this->loadModel($id);
		$model->scenario = "admin";
		$this->performAjaxValidation($model);

		if(isset($_POST[$modelClass]))
		{
			$model->attributes=$_POST[$modelClass];
			if($model->save()) {
				#$this->redirect(array('view','id'=>$model->id));
			}
		}
		$model->password = ""; // we don't pass the password to the view
		$this->render('update',array(
			'model'=>$model,
		));
	}
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionGroups($id)
	{
		$modelClass = Yii::app()->getModule("users")->userModelClass;
		$model=$this->loadModel($id);

		$this->render('groups',array(
			'model'=>$model,
		));
	}
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Manages all models.
	 */
	public function actionIndex()
	{
		$modelClass = Yii::app()->getModule("users")->userModelClass;
		$model=new $modelClass('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$modelClass]))
			$model->attributes=$_GET[$modelClass];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return User the loaded model
	 * @throws CHttpException if the model doesn't exist
	 */
	public function loadModel($id)
	{
		$modelClass = Yii::app()->getModule("users")->userModelClass;
		$model=$modelClass::model()->findByPk((int)$id);
		if($model===null) {
			throw new CHttpException(404,'The requested user does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}