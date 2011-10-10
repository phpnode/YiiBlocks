<?php

class FileController extends Controller {

	public function actions() {
		return array(
			"index" => array(
				"class" => "packages.webdav.actions.AWebDavFileServerAction",
				"directoryPath" => Yii::getPathOfAlias("packages.webdav.data.public"),
			),
		);
	}
	public function actionIndex2() {
		$basePath = Yii::app()->getModule("webdav")->getDirectoryPath();
		$publicDir = $basePath."public";
		$tmpDir = $basePath."tmp";
		// Create the root node
		$root = new Sabre_DAV_FS_Directory($publicDir);

		// The rootnode needs in turn to be passed to the server class
		$server = new Sabre_DAV_Server($root);

		$server->setBaseUri($this->createUrl("/webdav/file/index"));

		// Support for LOCK and UNLOCK
		$lockBackend = new Sabre_DAV_Locks_Backend_File($tmpDir . '/locksdb');
		$lockPlugin = new Sabre_DAV_Locks_Plugin($lockBackend);
		$server->addPlugin($lockPlugin);

		// Support for html frontend
		$browser = new Sabre_DAV_Browser_Plugin();
		$server->addPlugin($browser);

		// Authentication backend
		$authBackend = new AUserDavAuth();
		$auth = new Sabre_DAV_Auth_Plugin($authBackend,'SabreDAV');
		$server->addPlugin($auth);

		// Temporary file filter
		$tempFF = new Sabre_DAV_TemporaryFileFilterPlugin($tmpDir);
		$server->addPlugin($tempFF);
		// And off we go!
		$server->exec();
	}
}