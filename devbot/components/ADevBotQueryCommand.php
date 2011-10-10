<?php

class ADevBotQueryCommand extends ADevBotCommand {
	public $patterns = array(
		"/^<name>(,|.|!| )? what('s| is| are)? (.*)/i",
		"/^(,|.|!| )?what('s| is| are)? (.*)/i",


	);
	public function run(array $parameters) {
		$prefix = trim($parameters[2][0]);
		$subject = trim($parameters[3][0]);
		$subject = rtrim($subject,"?,!");
		return $subject." ".$prefix." fish";
	}
}