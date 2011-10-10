<?php
/**
 * A development robot that provides information about the system with a human like interface.
 *
 * @author Charles Pick
 * @package packages.devbot.components
 */
class ADevBot extends CComponent {
	/**
	 * Devbot's name
	 * @var string
	 */
	public $name = "devbot";
	/**
	 * An array of devbot command class names
	 * @var array
	 */
	public $classNames = array(
		"ADevBotGreetingCommand",
		"ADevBotQueryCommand",
		"ADevBotFactCommand",
	);

	/**
	 * Parses a command
	 * @param string $input the command to parse
	 * @return string devbot's response
	 */
	public function parse($input) {
		foreach($this->classNames as $className) {
			$command = new $className($this, $input);
			if (($response = $command->parse()) !== false) {
				return $response;
			}
		}
		return false;
	}

}