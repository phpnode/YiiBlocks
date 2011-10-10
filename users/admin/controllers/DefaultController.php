<?php
/**
 * The default controller for the user administration module
 * @author Charles Pick
 * @package packages.users.admin.controllers
 */
class DefaultController extends ABaseAdminController {
	/**
	 * Displays the default page for the user admin module
	 */
	public function actionIndex() {
		$this->render("index");
	}
}