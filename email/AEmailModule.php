<?php
Yii::import("packages.email.mailers.*");
/**
 * Provides email sending functionality
 * @author Charles Pick
 * @package packages.email
 */
class AEmailModule extends CWebModule {
	/**
	 * Holds the email sender
	 * @var AEmailSender
	 */
	protected $_sender;
	
	/**
	 * Gets the email sender
	 * @return AEmailSender the email sender to use
	 */
	public function getSender() {
		if ($this->_sender === null) {
			$this->_sender = new APHPEmailSender;
		}
		return $this->_sender;
	}
	/**
	 * Sets the email sender
	 * @param array|AEmailSender either an email sender instance or the configuration for one
	 * @return AEmailSender the email sender
	 */
	public function setSender($sender) {
		if (!is_object($sender) || !($sender instanceof AEmailSender)) {
			$config = $sender;
			if (isset($config['class'])) {
				$sender = new $config['class'];
				unset($config['class']);
			}
			else {
				$sender = new APHPEmailSender;
			}
			foreach($config as $key => $value) {
				$sender->{$key} = $value;
			}
		}
		$this->_sender = $sender;
		return $sender;
	}
}
