<?php
/**
 * A base class for email senders
 * @author Charles Pick
 * @package packages.email.mailers
 */
abstract class AEmailSender extends CComponent {
	
	/**
	 * Sends an email
	 * @param AEmail $email the email to send
	 * @return boolean whether the email was send successfully or not
	 */
	abstract public function send(AEmail $email);
	
}
