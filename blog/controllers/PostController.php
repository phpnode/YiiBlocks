<?php
/**
 * The PostController controller class deals with viewing and managing {@link ABlogPost} models
 * @package application.modules.admin.modules.blog.controllers
 */
class PostController extends Controller
{

	public function actionFeed() {
		Yii::import("packages.feeds.*");
		$feed = new ARssFeed();
		$modelClass = "ABlogPost";
		$criteria = new CDbCriteria;
		$criteria->limit = 20;
		foreach($modelClass::model()->feedItems()->findAll($criteria) as $model) {
			$feed->add($model->getFeedItem());
		}
		$feed->render();
		die();
	}

	/**
	 * Displays a particular blog post.
	 * @param string $slug the slug of the post to be shown
	 */
	public function actionView($slug)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($slug),
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider = new CActiveDataProvider(ABlogPost::model()->published()->newestFirst());
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param slug $slug the slug of the model to be loaded
	 * @return ABlogPost the loaded model
	 * @throws CHttpException if the model doesn't exist
	 */
	public function loadModel($slug)
	{
		$model=ABlogPost::model()->findBySlug($slug);
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
