<?php
/**
 * Abstract controller class to deal with user related functions such as registering, recovering passwords etc.
 * 
 * 
 * 
 * @author Charles Pick
 * @package packages.users.controllers
 */
abstract class AUserController extends Controller {
	/**
	 * Declares class based actions.
	 * @return array the class based action configuration
	 */
	public function actions() {
		return array(
			"login" => array(
				"class" => "packages.users.components.ALoginAction"
			),
		);
	}
	/**
	 * Registers a new user 
	 */
	public function actionRegister() {
		$usersModule = Yii::app()->getModule("users");
	 	$modelClass = $usersModule->userModelClass;
		$model = new $modelClass("register");
		if (isset($_POST[$modelClass])) {
			
			$model->attributes = $_POST[$modelClass];
			if ($model->save()) {
				
				$this->redirect($usersModule->redirectAfterRegistration);
			}
		}
		if (Yii::app()->request->isAjaxRequest) {
			$this->renderPartial("register",array("model" => $model),false,true);
		}
		else {
			$this->render("register",array("model" => $model));
		}
	}
	
	/**
	 * The user's account page
	 */
	public function actionAccount() {
		$model = Yii::app()->user->model;
		if (Yii::app()->request->isAjaxRequest) {
			$this->renderPartial("account",array("model" => $model),false,true);
		}
		else {
			$this->render("account",array("model" => $model));
		}
	}
	
	/**
	 * Allows a user to reset their password if they've forgotten it.
	 * The user enters their email address and we send them a link with
	 * a unique key. When they click this link they're presented with
	 * a form to reset their password. After reseting their password successfully
	 * we log them in and redirect them to their account page.
	 * @param integer $id The id of this user
	 * @param string $key The unique key for this user
	 */
	public function actionResetPassword($id = null, $key = null) {
		$usersModule = Yii::app()->getModule("users");
		$modelClass = $usersModule->userModelClass;
		if ($id !== null && $key !== null) {
			// check if the id + key match for this user
			$user = $modelClass::model()->findByPk($id);
			if (!is_object($user)) {
				Yii::log("Invalid password reset attempt (no such user)","warning","user.activity.resetPassword");
				throw new CHttpException(500,"Your request is invalid");
			}
			elseif($user->passwordResetCode != $key) {
				Yii::log("[$user->id] Invalid password reset attempt (invalid code)","warning","user.activity.resetPassword");
				throw new CHttpException(500,"Your request is invalid");
			}
			// now the user needs to change their password
			$user->scenario = "newPassword";
			if (isset($_POST[$modelClass])) {
				$user->attributes = $_POST[$modelClass];
				if ($user->save()) {
					Yii::log("[$user->id] Password reset via email","info","user.activity.resetPassword");
					$identityClass = $usersModule->identityClass;
					$identity = new $identityClass($user->email);
					$identity->id = $user->id;
					$identity->name = $user->name;
					if ($usersModule->autoLoginByDefault) {
						Yii::app()->user->login($identity,$usersModule->autoLoginDuration);
					}
					else {
						Yii::app()->user->login($identity,0);
					}
					Yii::app()->user->setFlash("success","<h2>Your password was changed</h2>");
					$this->redirect(array("/users/user/account"));
				}
			}
			$user->password = "";
			$this->render("newPassword",array("model" => $user));
			
			return;
		}
		
		$model = new $modelClass("resetPassword");
		if (isset($_POST[$modelClass])) {
			$user = $modelClass::model()->findByAttributes(array("email" => $_POST[$modelClass]['email']));
			if (is_object($user)) {
				// send the user a password reset email
				
				$email = new AEmail;
				$email->recipient = $user->email;
				$email->viewData = array("user" => $user);
				$email->view = "/user/emails/resetPassword";
				if ($email->send() || true) {
					Yii::app()->user->setFlash("info",$this->renderPartial("flashMessages/resetEmailSent",array("user" => $user),true));
					$this->redirect(array("/site/index"));
				}
				else {
					$model->addError("email", "There was a problem sending email to this address");
				}
			}
			else {
				$model->addError("email","We couldn't find a user with that email address");
			}
		}
		if (Yii::app()->request->isAjaxRequest) {
			$this->renderPartial("resetPassword",array("model" => $model),false,true);
		}
		else {
			$this->render("resetPassword",array("model" => $model));
		}
	}
	/**
	 * Activates the user's account.
	 * This is used when AUserModule.requireActivation is true.
	 * After the code has been verified, the user's account will be activated and the
	 * user will be logged in and taken to their account page. 
	 * @param integer $id The user's id
	 * @param string $key The unique activation code for this user
	 */
	public function actionActivate($id,$key) {
		$usersModule = Yii::app()->getModule("users");
		$modelClass = $usersModule->userModelClass;
		// check if the id + key match for this user
		$user = $modelClass::model()->findByPk($id);
		if (!is_object($user)) {
			Yii::log("Invalid account activation attempt (no such user)","warning","user.activity.activateAccount");
			throw new CHttpException(500,"Your request is invalid");
		}
		elseif($user->activationCode != $key) {
			Yii::log("[$user->id] Invalid account activation attempt (invalid code)","warning","user.activity.activateAccount");
			throw new CHttpException(500,"Your request is invalid");
		}
		elseif($user->isActive) {
			Yii::log("[$user->id] Invalid account activation attempt (already active)","warning","user.activity.activateAccount");
			Yii::app()->user->setFlash("info","<h2>You account is already active</h2><p>Your account has already been activated, please login to continue</p>");
			$this->redirect(Yii::app()->user->loginUrl);
			return;
		}
		if (!$user->activate()) {
			Yii::app()->user->setFlash("error","<h2>There was a problem activating your account</h2>");
			$this->redirect(array("/site/index"));
		}
		// now we need to log this user in
		$identityClass = $usersModule->identityClass;
		$identity = new $identityClass($user->email);
		$identity->id = $user->id;
		$identity->name = $user->name;
		if ($usersModule->autoLoginByDefault) {
			Yii::app()->user->login($identity,$usersModule->autoLoginDuration);
		}
		else {
			Yii::app()->user->login($identity,0);
		}
		Yii::app()->user->setFlash("success","<h2>Your account has been activated successfully!</h2>");
		$this->redirect(array("/users/user/account"));
	}
	
}
