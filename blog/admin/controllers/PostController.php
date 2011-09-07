<?php
/**
 * The PostController controller class deals with viewing and managing {@link ABlogPost} models
 * @package application.modules.admin.modules.blog.controllers
 */
class PostController extends ABaseAdminController
{
	public function actions() {
		return array(
			"tag" => array(
				"class" => "packages.tags.components.ATagAction",
				"modelClass" => "ABlogPost",
			)
		);
	}
	/**
	 * Performs a bulk action on specified blog posts
	 */
	public function actionBulk() {
		if (!Yii::app()->request->isPostRequest) {
			throw new CHttpException(500,"Invalid Request");
		}
		$criteria = new CDbCriteria;
		$criteria->addInCondition("id", $_POST['Post']);
		foreach(ABlogPost::model()->findAll($criteria) as $post) {
			if (isset($_POST['publish'])) {
				$post->publish()->save();
			}
			elseif (isset($_POST['archive'])) {

				$post->status = "archived";
				$post->save();
			}
			elseif (isset($_POST['delete'])) {
				$post->delete();
			}
		}
		$this->redirect(array("index"));
	}

	/**
	 * Attempts to summarize a given text
	 * @param integer $limit The maximum number of characters to return
	 */
	public function actionSummarize($limit = 500) {
		if (isset($_POST['ABlogPost']['content'])) {

			echo Yii::app()->textProcessor->summarize(html_entity_decode(htmlspecialchars_decode(trim(strip_tags($_POST['ABlogPost']['content'])), ENT_QUOTES), ENT_QUOTES, Yii::app()->charset), $limit);
		}

	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new ABlogPost;
		$scenarios = array("draft" => "draft", "published" => "publish", "archived" => "archive");
		if (isset($_POST['ABlogPost']['status']) && isset($scenarios[$_POST['ABlogPost']['status']])) {
			$model->scenario = $scenarios[$_POST['ABlogPost']['status']];
		}
		$this->performAjaxValidation($model);

		if(isset($_POST['ABlogPost']))
		{
			$model->attributes=$_POST['ABlogPost'];
			if($model->save()) {
				Yii::app()->user->setFlash("success","<h2>Your changes were saved successfully</h2>");
				$this->redirect(array('update','id'=>$model->id));
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
		$model=$this->loadModel($id);
		$scenarios = array("draft" => "draft", "published" => "publish", "archived" => "archive");
		if (isset($_POST['ABlogPost']['status']) && isset($scenarios[$_POST['ABlogPost']['status']])) {
			$model->scenario = $scenarios[$_POST['ABlogPost']['status']];
		}
		$this->performAjaxValidation($model);

		if(isset($_POST['ABlogPost']))
		{
			$model->attributes=$_POST['ABlogPost'];
			if($model->save()) {
				Yii::app()->user->setFlash("success","<h2>Your changes were saved successfully</h2>");
				$this->redirect(array('update','id'=>$model->id));
			}
		}

		$this->render('update',array(
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
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new ABlogPost('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ABlogPost']))
			$model->attributes=$_GET['ABlogPost'];

		$this->render('index',array(
			'model'=>$model,
		));
	}



	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return ABlogPost the loaded model
	 * @throws CHttpException if the model doesn't exist
	 */
	public function loadModel($id)
	{
		$model=ABlogPost::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='blog-post-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
