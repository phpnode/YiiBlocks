<?php
/**
 * Allows users to create, edit and view comments
 * @author Charles Pick
 * @package packages.comments.controllers
 */
class CommentController extends Controller {
	/**
	 * Creates a comment owned by the given model.
	 * @param string $ownerModel The class name of the owner model that owns this comment
	 * @param integer $ownerId The ID of the model that owns this comment
	 */
	public function actionCreate($ownerModel, $ownerId) {
		$owner = $ownerModel::model()->findByPk($ownerId);
		if (!is_object($owner) || (!($owner instanceof IACommentable) && !is_object($owner->asa("ACommentable")))) {
			throw new CHttpException(404,Yii::t("blocks","Invalid Request"));
		}

		$model = new AComment();
		$this->performAjaxValidation($model);
		if (isset($_POST['AComment'])) {
			$model->attributes = $_POST['AComment'];
			if ($owner->addComment($model)) {
				Yii::app()->user->setFlash("success",Yii::t("packages.comments","<h3>Thanks, your comment was added</h3><p>Your comment was added, thanks for your contribution</p>"));
				if (($owner instanceof IALinkable) || is_object($owner->asa("ALinkable"))) {
					$this->redirect($owner->createUrl());
				}
				else {
					$this->redirect(Yii::app()->getModule("comments")->returnUrl);
				}
			}
		}


		if (Yii::app()->request->isAjaxRequest) {
			echo $this->renderPartial("_create",array("model" => $model, "owner" => $owner),true, true);
		}
		else {
			$this->render("create",array("model" => $model, "owner" => $owner));
		}

	}
	/**
	 * Updates a comment by the current user
	 * @param integer $id The id of the comment
	 * @param string $ownerModel The class name of the owner model that owns this comment
	 * @param integer $ownerId The ID of the model that owns this comment
	 */
	public function actionUpdate($id, $ownerModel, $ownerId) {
		$owner = $ownerModel::model()->findByPk($ownerId);
		if (!is_object($owner) || (!($owner instanceof IACommentable) && !is_object($owner->asa("ACommentable")))) {
			throw new CHttpException(404,Yii::t("blocks","Invalid Request"));
		}

		$model = AComment::model()->byCurrentUser()->findByPk($id);
		if (!is_object($model)) {
			throw new CHttpException(404,Yii::t("blocks","Invalid Request"));
		}
		$this->performAjaxValidation($model);
		if (isset($_POST['AComment'])) {
			$model->attributes = $_POST['AComment'];
			if ($model->save()) {
				Yii::app()->user->setFlash("success",Yii::t("packages.comments","<h3>Thanks, your comment was updated</h3><p>Your comment was updated, thanks for your contribution</p>"));
				if (($owner instanceof IALinkable) || is_object($owner->asa("ALinkable"))) {
					$this->redirect($owner->createUrl());
				}
				else {
					$this->redirect(Yii::app()->getModule("comments")->returnUrl);
				}
			}
		}


		if (Yii::app()->request->isAjaxRequest) {
			echo $this->renderPartial("_update",array("model" => $model, "owner" => $owner),true, true);
		}
		else {
			$this->render("update",array("model" => $model, "owner" => $owner));
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='acomment-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
