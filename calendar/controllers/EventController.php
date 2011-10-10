<?php
/**
 * The EventController controller class deals with viewing and managing {@link ACalendarEvent} models
 * @package application.controllers
 */
class EventController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * Specifies the action filters for this controller
	 * This method returns an array of filter specifications. Each array element specifies a single filter.
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
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
	public function actionCreate($calendarId, $title = null, $start = null, $end = null, $allDay = null)
	{
		$model=new ACalendarEvent;
		$model->calendarId = $calendarId;
		if ($title) {
			$model->title = $title;
		}
		if ($start) {
			$model->startsAt = $start / 1000;
		}
		if ($end) {
			$model->endsAt = $end / 1000;
		}
		elseif ($allDay == "true") {

			$model->endsAt = mktime(23,59,59,date("m",$model->startsAt),date("d",$model->startsAt),date("Y",$model->startsAt));
		}
		if ($allDay == "true") {
			$model->allDay = true;
		}
		$this->performAjaxValidation($model);

		if(isset($_POST['ACalendarEvent']))
		{
			$model->attributes=$_POST['ACalendarEvent'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}
		if (Yii::app()->request->isAjaxRequest) {
			$this->renderPartial('quickcreate',array(
				'model'=>$model,
			),false,true);
		}
		else {
			$this->render('create',array(
				'model'=>$model,
			));
		}
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ACalendarEvent']))
		{
			$model->attributes=$_POST['ACalendarEvent'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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
		$dataProvider=new CActiveDataProvider('ACalendarEvent');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new ACalendarEvent('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ACalendarEvent']))
			$model->attributes=$_GET['ACalendarEvent'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return ACalendarEvent the loaded model
	 * @throws CHttpException if the model doesn't exist
	 */
	public function loadModel($id)
	{
		$model=ACalendarEvent::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='acalendar-event-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}