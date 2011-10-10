<?php
Yii::import("packages.devbot.components.*");
/**
 * Provides command line access to devbot
 * @author Charles Pick
 * @package packages.devbot.commands
 */
class ADevBotCLI extends CConsoleCommand {
	/**
	 * Starts devbot
	 */
	public function actionStart($command = "Hi Devbot") {
		$bot = new ADevBot();
		echo $bot->parse($command)."\n";
	}
}