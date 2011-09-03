<?php
/**
 * Displays a login form and logs the user in to the site
 * @package packages.users.components
 * @author Charles Pick
 */
class ALoginAction extends CAction {
	/**
	 * Displays the login form and authenticates the user
	 */
	public function run() {
		$loginFormClass = Yii::app()->getModule("users")->loginFormClass;
		$loginForm = new $loginFormClass;
		$controller = $this->controller;
		if (isset($_POST[$loginFormClass])) {
			$loginForm->attributes = $_POST[$loginFormClass];
			if ($loginForm->validate()) {
				$loginForm->login();
				$controller->redirect(isset(Yii::app()->user->returnUrl) ? Yii::app()->user->returnUrl : array("/users/user/account"));
			}
		}
		
		if (Yii::app()->request->isAjaxRequest) {
			$controller->renderPartial("login",array("model" => $loginForm), false, true);
		}
		else {
			$controller->render("login",array("model" => $loginForm));
		}
	}
}
