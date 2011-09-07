<?php
/**
 * The default controller for the elastic search module
 *
 */
class ElasticSearchController extends ABaseAdminController {
	/**
	 * Shows the elastic search overview
	 */
	public function actionIndex() {
		$this->render("index");
	}
}