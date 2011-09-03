<?php
/**
 * @package packages.rbac.controllers
 * @author Charles Pick
 */
class RbacController extends AAdminBaseController {
	/**
	 * The default index page for the role based access control manager.
	 * Shows a list of links to roles, tasks, operations
	 */
	public function actionIndex() {
		$this->render("index");
	}
}