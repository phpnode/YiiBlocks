<?php
Yii::import("packages.textProcessor.*");
/**
 * ATextProcessor is an application component that provides various
 * functions for processing text
 * @package packages.textProcessor
 * @author Charles Pick
 */
class ATextProcessor extends CApplicationComponent {
	/**
	 * Whether to use Open Text Summarizer or not
	 * Defaults to false, meaning use a (less accurate) PHP function instead
	 * @var boolean
	 */
	public $useOpenTextSummarizer = false;

	/**
	 * The path to the OTS binary with trailing slash, if null OTS is assumed to be in the current path
	 * @var string
	 */
	public $openTextSummarizerPath;

	/**
	 * Summarizes a string, will use Open Text Summarizer if possible, otherwise falls
	 * back to a less accurate PHP summarizer
	 * @param string $text The text to summarize
	 * @param integer $maxLength The maximum length of the summarized string in characters
	 * @return string The summarized text
	 */
	public function summarize($text, $maxLength = 500) {

		if (mb_strlen($text) <= $maxLength) {
			return $text;
		}
		if (true || $this->useOpenTextSummarizer) {
			$cmd = str_replace(array('\\', '%'), array('\\\\', '%%'), $text);
			$cmd = escapeshellarg($cmd);

			$cmd = "printf $cmd | ".$this->openTextSummarizerPath."ots --ratio ".floor(($maxLength / mb_strlen($text)) * 100);

			return $this->truncate(trim(shell_exec($cmd)),$maxLength);
		}
		else {
			$summarizer = new ATextSummarizer();
			return $this->truncate(trim($summarizer->summarize($text,($maxLength / mb_strlen($text)))), $maxLength);
		}
	}

	/**
	 * Truncates a string to a certain length
	 * @param string $text The text to truncate
	 * @param integer $maxLength The maximum length of the string
	 * @return string The truncated text
	 */
	public function truncate($text, $maxLength = 500) {
		$text =  preg_replace('|\s{2,}|', ' ', $text);
		$text = trim(preg_replace('|  +|', ' ', $text));
		if (mb_strlen($text) <= $maxLength) {
			return $text;
		}
		$text = mb_substr($text, 0, $maxLength - 3);
		$text .= "...";
		return $text;
	}
}
