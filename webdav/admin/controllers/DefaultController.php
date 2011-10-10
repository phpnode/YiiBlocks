<?php

class DefaultController extends ABaseAdminController {
	public function actions() {
		return array(
			"index" => array(
				"class" => "packages.webdav.actions.AWebDavFileServerAction",
				"directoryPath" => Yii::getPathOfAlias("webroot"),
			),
		);
	}
}