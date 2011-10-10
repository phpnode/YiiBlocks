<?php

class ADevBotFactCommand extends ADevBotCommand {
	public $patterns = array(
		"/^<name>(,|.|!| )? (.*) (is|are|was) (.*)/i",
		"/^ ?(.*) (is|are|was) (.*)/i",
	);
	public function run(array $parameters) {
		$subject = trim($parameters[1][0]);
		$article = trim($parameters[2][0]);
		$thing = trim($parameters[3][0]);
		return "orly, $subject $article $thing?";
	}
}