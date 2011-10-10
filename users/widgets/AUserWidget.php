<?php
/**
 * A base class for widgets specific to users
 * @author Charles Pick
 * @package packages.users.widgets
 */
abstract class AUserWidget extends CWidget {
	/**
	 * Holds the user that this widget shows information for
	 * @var AUser
	 */
	public $user;
}