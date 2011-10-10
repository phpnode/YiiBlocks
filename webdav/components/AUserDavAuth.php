<?php
/**
 * DAV authentication backend class that logs in using a user identity
 * @author Charles Pick
 * @package packages.webdav.components
 */
class AUserDavAuth extends Sabre_DAV_Auth_Backend_AbstractBasic {
	/**
	 * Validates a username and password
	 * @param string $username the username
	 * @param string $password the plain text password
	 * @return boolean
	 */
	protected function validateUserPass($username, $password) {
		$usersModule = Yii::app()->getModule("users");
		$identityClass = $usersModule->identityClass;
		$identity = new $identityClass($username, $password);
		return $identity->authenticate();
	}
}