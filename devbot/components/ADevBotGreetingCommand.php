<?php

class ADevBotGreetingCommand extends ADevBotCommand {
	public $patterns = array(
		"/^(hi|hello|yo|sup) <name>(,|.|!)?(.*)/i",
	);
	public function run(array $parameters) {
		$response = "Hi";
		$parameters[3][0] = trim($parameters[3][0]);
		if ($parameters[3][0] != "") {
			// treat anything else as a seperate command
			if (($subcommand = $this->owner->parse($parameters[3][0])) !== false) {
				$response .= ", ".$subcommand;
			}
		}
		return $response;
	}
}