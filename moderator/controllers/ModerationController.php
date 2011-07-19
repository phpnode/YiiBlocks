<?php
/**
 * Provides an interface for moderators to moderate items.
 * @author Charles Pick
 * @package blocks.moderator
 */
class ModerationController extends Controller {
	public function actionIndex() {
		$model = new AModerationItem("search");
		$model->unsetAttributes();
		if (isset($_GET['AModerationItem'])) {
			$model->attributes = $_GET['AModerationItem'];
		}
		$this->render("index",array("model" => $model));
	}
}
