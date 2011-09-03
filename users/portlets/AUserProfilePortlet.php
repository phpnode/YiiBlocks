<?php
Yii::import("zii.widgets.CPortlet");
/**
 * A base class for portlets shown either on the user's public profile page or their account page.
 * @author Charles Pick
 * @package packages.users.portlets
 */
class AUserProfilePortlet extends CPortlet {
	/**
	 * Holds the user that this portlet shows information for
	 * @var AUser
	 */
	public $user;
}
