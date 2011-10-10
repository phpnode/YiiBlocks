<?php
/**
 * A base class for commands sent to devbot
 * @author Charles Pick
 * @package packages.devbot.commands
 */
abstract class ADevBotCommand extends CComponent {
	/**
	 * The input command
	 * @var string
	 */
	public $input;

	/**
	 * The devbot this command is being run against
	 * @var ADevBot
	 */
	public $owner;

	/**
	 * The patterns used to match this command
	 * @var array
	 */
	public $patterns = array();

	/**
	 * Constructor
	 * @param ADevBot $owner The bot this command belongs to
	 * @param string $input The input command
	 */
	public function __construct(ADevBot $owner, $input = null) {
		$this->owner = $owner;
		$this->input = $input;
	}
	/**
	 * Parses a command and returns the response if possible
	 * @return bool|string either false if the command wasn't matched, or a string representing the response
	 */
	public function parse() {
		$input = $this->input;
		foreach($this->patterns as $pattern) {
			if (preg_match_all("/<(.*)>/",$pattern,$tokens)) {
				foreach($tokens[0] as $i => $token) {
					$property = $tokens[1][$i];
					$pattern = strtr($pattern,array($tokens[0][$i] => $this->owner->{$property}));
				}
			}
			if (preg_match_all($pattern, $input,$matches)) {
				return $this->run($matches);
			}
		}
		return false;
	}


	/**
	 * Runs the command
	 * @param array $parameters the parameters to pass to this command
	 * @return string The response to the command
	 */
	abstract public function run(array $parameters);
}