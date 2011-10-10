<?php
/**
 * The CalendarController controller class deals with viewing and managing {@link ACalendar} models
 * @package application.controllers
 */
class CalendarController extends Controller
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
				'actions'=>array('index','view', 'eventData'),
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
		#echo "<pre>";
		#print_r(Yii::app()->locale);
		#echo "</pre>";
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
		$model=new ACalendar;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ACalendar']))
		{
			$model->attributes=$_POST['ACalendar'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ACalendar']))
		{
			$model->attributes=$_POST['ACalendar'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Gets event data for a particular calendar
	 * @param integer $id the ID of the calendar to get events from
	 * @param integer $start The start time
	 * @param integer $end The end time
	 */
	public function actionEventData($id, $start, $end)
	{
		$model=$this->loadModel($id);
		$start = (int) $start;
		$end = (int) $end;
		$now = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
		$startMonth = (int) date("m",$start);
		$endMonth = (int) date("m",$end);
		if ($startMonth == $endMonth) {
			$endDay = (int) date("d", $end);
			$startDay =(int)  date("d",$start);
		}
		else {
			$startDay = 1;
			$endDay = 31;
		}
		$criteria = new CDbCriteria;
		$criteria->condition = "
			calendarId = $model->id AND (
				(startsAt >= $start AND endsAt < $end)
					OR (
						(type = 'yearly' AND MONTH(FROM_UNIXTIME(startsAt)) IN (".implode(",",range($startMonth,$endMonth)).") AND DAYOFMONTH(FROM_UNIXTIME(startsAt)) IN (".implode(",",range($startDay,$endDay))."))

					)
			)";

		$response = new AJSONResponse();
		foreach(ACalendarEvent::model()->findAll($criteria) as $event) {
			$response[] = array(
				"id" => $event->id,
				"title" => $event->title,
				"url" => $event->createUrl(),
				"start" => date("Y-m-d H:i:s",$event->startsAt),
				"end" => date("Y-m-d H:i:s",$event->endsAt),
				"allDay" => $event->allDay,
			);
		}
		$response->render();
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
		$dataProvider=new CActiveDataProvider('ACalendar');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new ACalendar('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ACalendar']))
			$model->attributes=$_GET['ACalendar'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return ACalendar the loaded model
	 * @throws CHttpException if the model doesn't exist
	 */
	public function loadModel($id)
	{
		$model=ACalendar::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='acalendar-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}