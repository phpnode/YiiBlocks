<?php
/**
 * Administration functions for the voting module.
 * @package blocks.voting.controllers
 * @author Charles Pick
 */
class VoteAdminController extends CController {
	public function actionIndex() {
		$this->render("index");
	}
}
