<?php
/**
 * Deals with voting for models
 * @author Charles Pick
 * @package packages.voting.controllers
 */
class VoteController extends CController {
	
	/**
	 * Registers an upvote for the given model.
	 * @param string $ownerModel The class name of the owner model
	 * @param integer $ownerId The ID of the owner
	 */
	public function actionUp($ownerModel, $ownerId) {
		if (!Yii::app()->request->isPostRequest || !class_exists($ownerModel)) {
			throw new CHttpException(500,Yii::t("blocks","Invalid Request"));
		}
		$owner = $ownerModel::model()->findByPk($ownerId);
		if (!is_object($owner)) {
			throw new CHttpException(404,Yii::t("blocks","No such page"));
		}
		$result = ($owner->upVote() ? "upvoted" : ($owner->resetVote() ? "notvoted" : "ERROR"));
		if (Yii::app()->request->isAjaxRequest) {
			$response = new AJSONResponse();
			$response->score = $owner->totalVoteScore + 1;
			$response->totalUpvotes = $owner->totalUpvotes;
			$response->totalDownvotes = $owner->totalDownvotes; 
			$response->status = $result;
			$response->render();
			return;
		}
		else {
			if ($result === "ERROR") {
				Yii::app()->user->setFlash("error",Yii::t("blocks","<h3>Voting Error</h3><p>There was a problem registering your vote.</p>"));
			}
			else {
				Yii::app()->user->setFlash("success",Yii::t("blocks","<h3>Thanks for voting!</h3><p>Your vote was registerd, thanks.</p>"));
			}
			$this->redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : array("/site/index"));
		}
	}
	
	/**
	 * Registers a downvote for the given model.
	 * @param string $ownerModel The class name of the owner model
	 * @param integer $ownerId The ID of the owner
	 */
	public function actionDown($ownerModel, $ownerId) {
		if (!Yii::app()->request->isPostRequest || !class_exists($ownerModel)) {
			throw new CHttpException(500,Yii::t("blocks","Invalid Request"));
		}
		$owner = $ownerModel::model()->findByPk($ownerId);
		if (!is_object($owner)) {
			throw new CHttpException(404,Yii::t("blocks","No such page"));
		}
		$result = ($owner->downVote() ? "downvoted" : ($owner->resetVote() ? "notvoted" : "ERROR"));
		if (Yii::app()->request->isAjaxRequest) {
			$response = new AJSONResponse();
			$response->score = $owner->totalVoteScore + 1;
			$response->totalUpvotes = $owner->totalUpvotes;
			$response->totalDownvotes = $owner->totalDownvotes; 
			$response->status = $result;
			$response->render();
			return;
		}
		else {
			if ($result === "ERROR") {
				Yii::app()->user->setFlash("error",Yii::t("blocks","<h3>Voting Error</h3><p>There was a problem registering your vote.</p>"));
			}
			else {
				Yii::app()->user->setFlash("success",Yii::t("blocks","<h3>Thanks for voting!</h3><p>Your vote was registerd, thanks.</p>"));
			}
			$this->redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : array("/site/index"));
		}
	}
	
	/**
	 * Resets a vote for the given model.
	 * @param string $ownerModel The class name of the owner model
	 * @param integer $ownerId The ID of the owner
	 */
	public function actionReset($ownerModel, $ownerId) {
		if (!Yii::app()->request->isPostRequest || !class_exists($ownerModel)) {
			throw new CHttpException(500,Yii::t("blocks","Invalid Request"));
		}
		$owner = $ownerModel::model()->findByPk($ownerId);
		if (!is_object($owner)) {
			throw new CHttpException(404,Yii::t("blocks","No such page"));
		}
		$result = ($owner->resetVote() ? "notvoted" : ($owner->resetVote() ? "notvoted" : "ERROR"));
		if (Yii::app()->request->isAjaxRequest) {
			$response = new AJSONResponse();
			$response->score = $owner->totalVoteScore + 1;
			$response->totalUpvotes = $owner->totalUpvotes;
			$response->totalDownvotes = $owner->totalDownvotes; 
			$response->status = $result;
			$response->render();
			return;
		}
		else {
			if ($result === "ERROR") {
				Yii::app()->user->setFlash("error",Yii::t("blocks","<h3>Voting Error</h3><p>There was a problem registering your vote.</p>"));
			}
			else {
				Yii::app()->user->setFlash("success",Yii::t("blocks","<h3>Thanks for voting!</h3><p>Your vote was registerd, thanks.</p>"));
			}
			$this->redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : array("/site/index"));
		}
	}
}
