<?php
/**
 * The BenchmarkController controller class deals with viewing and managing {@link ABenchmark} models
 * @package application.controllers
 */
class BenchmarkController extends ABaseAdminController
{

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model = $this->loadModel($id);
		$dataProvider = new CActiveDataProvider(
			ABenchmarkResult::model(),
			array(
				"criteria" => array(
					"condition" => "benchmarkId = :benchmarkId",
					"params" => array(":benchmarkId" => $model->id),
				),
				"sort" => array(
					"defaultOrder" => "id DESC",
				),
				"pagination" => array(
					"pageSize" => 20,
				)
			)
		);
		$this->render('view',array(
			'model'=>$model,
			'dataProvider' => $dataProvider,
		));
	}

	/**
	 * Runs a specific benchmark
	 * @param integer $id the ID of the benchmark to run
	 */
	public function actionRun($id)
	{
		$model = $this->loadModel($id);
		if (Yii::app()->request->getIsAjaxRequest()) {
			// run the benchmark

		}
		else {
			$this->render('run',array(
				'model'=>$model,
			));
		}
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new ABenchmark;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ABenchmark']))
		{
			$model->attributes=$_POST['ABenchmark'];
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

		if(isset($_POST['ABenchmark']))
		{
			$model->attributes=$_POST['ABenchmark'];
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
		$model=new ABenchmark('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ABenchmark']))
			$model->attributes=$_GET['ABenchmark'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return ABenchmark the loaded model
	 * @throws CHttpException if the model doesn't exist
	 */
	public function loadModel($id)
	{
		$model=ABenchmark::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='abenchmark-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}