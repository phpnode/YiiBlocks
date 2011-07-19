<?php
/**
 * Provides an interface for moderators to moderate items.
 * @author Charles Pick
 * @package blocks.moderator
 */
class ModerationController extends Controller {
	/**
	 * Shows a list of moderation items. 
	 */
	public function actionIndex() {
		$model = new AModerationItem("search");
		$model->unsetAttributes();
		if (isset($_GET['AModerationItem'])) {
			$model->attributes = $_GET['AModerationItem'];
		}
		$this->render("index",array("model" => $model));
	}
	/**
	 * Approves a moderation item
	 * @param string $ownerModel The name of the moderated item class
	 * @param integer $ownerId The id of the moderated item to approve
	 */
	public function actionApprove($ownerModel, $ownerId) {
		if (Yii::app()->request->isPostRequest && $this->loadModel($ownerModel, $ownerId)->approve()) {
			if (Yii::app()->request->isAjaxRequest) {
				$response = new AJSONResponse(array(
						"status" => "approved"
					));
				$response->render();
				return;
			}
			else {
				Yii::app()->user->setFlash("success", "<h3>Item Approved</h3>");
				$this->redirect(array("index"));
			}
		}
		else {
			throw new CHttpException(500,Yii::t("blocks","There was a problem processing your request."));
		}
	}
	
	/**
	 * Denies a moderation item
	 * @param string $ownerModel The name of the moderated item class
	 * @param integer $ownerId The id of the moderated item to deny
	 */
	public function actionDeny($ownerModel, $ownerId) {
		if (Yii::app()->request->isPostRequest && $this->loadModel($ownerModel, $ownerId)->deny()) {
			if (Yii::app()->request->isAjaxRequest) {
				$response = new AJSONResponse(array(
						"status" => "denied"
					));
				$response->render();
				return;
			}
			else {
				Yii::app()->user->setFlash("success", "<h3>Item Approved</h3>");
				$this->redirect(array("index"));
			}
		}
		else {
			throw new CHttpException(500,Yii::t("blocks","There was a problem processing your request."));
		}
	}
	
	
	/**
	 * Loads a model based on the given id.
	 * @param string $ownerModel The name of the moderated item class
	 * @param integer $ownerId The id of the moderated item
	 * @return AModerationItem the loaded model
	 * @throws CHttpException if the model doesn't exist
	 */
	protected function loadModel($ownerModel, $ownerId) {
		$model = AModerationItem::model()->findByAttributes(array(
					"ownerModel" => $ownerModel,
					"ownerId" => $ownerId
				));
		if (!is_object($model)) {
			throw new CHttpException(404,Yii::t("blocks", "No such item"));
		}
		return $model;
	}
}
