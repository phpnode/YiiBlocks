<?php
Yii::import("packages.comments.interfaces.*");
Yii::import("packages.comments.models.*");
Yii::import("packages.comments.components.*");
/**
 * Holds functionality related to comments.
 * Comments can be attached to other models and are managed via the comment controller.
 * @package packages.reviews
 * @author Charles Pick
 */
class ACommentsModule extends CWebModule {

	/**
	 * The name of the comments table.
	 * Defaults to "comments".
	 * @var string
	 */
	public $commentTable = "comments";

	/**
	 * Whether comments are votable or not.
	 * If set to true like/dislike buttons will be shown on comments.
	 * Defaults to true.
	 * @var boolean
	 */
	public $votableComments = true;

	/**
	 * The action used when voting for comments.
	 * This only applies if {@link votableComments $this->votableComments} is true.
	 * @var string
	 */
	public $voteAction = "/voting/vote";


	/**
	 * When a captcha is required when posting comments.
	 * Either:
	 * <li>"always" - meaning always require a captcha.</li>
	 * <li>"guests" - meaning only require a captcha for guest users.</li>
	 * <li>"never" - never require a captcha.</li>
	 * Defaults to always
	 * @var string
	 */
	public $requireCaptcha = "always";

	/**
	 * The captcha action, used to display the captcha image
	 * Defaults to "/site/captcha"
	 * @var string
	 */
	public $captchaAction = "/site/captcha";

	/**
	 * Whether users must be logged in to comment on items.
	 * Defaults to false.
	 * @var boolean
	 */
	public $requiresLogin = false;

	/**
	 * Whether to use akismet blog comment spam filtering.
	 * Uses the AAkismet application component.
	 * Defaults to false.
	 * @see AAkismet
	 * @var boolean
	 */
	public $useAkismet = false;

	/**
	 * Whether comments should be moderated before they go live.
	 * Defaults to true
	 */
	public $moderateComments = true;

	/**
	 * Whether to use the nested set model when storing comments.
	 * If false, a simple (adjacency list) parent -> child relationship will be used, which can
	 * become slow when retrieving deeply nested comments, but is generally
	 * quick to write to and works reasonably well when replies are not deeply nested.
	 * Nested set is much faster when reading nested comment threads, but writes are
	 * more expensive and the table layout can seem intimidating for beginner programmers.
	 * Defaults to true
	 * @var boolean
	 */
	public $useNestedSet = true;

	/**
	 * Whether comments can be replied to or not.
	 * Defaults to true.
	 * @var boolean
	 */
	public $allowReplies = true;

	/**
	 * The maximum depth of nested comment threads.
	 * When a thread reaches this level, no more reply links will be shown
	 * and any further comments will be added to the nearest parent.
	 * This is useful to prevent deeply nested comments outgrowing the usable space on the screen.
	 * If this property is null no limit is applied.
	 * Defaults to 5.
	 * @var integer
	 */
	public $nestLimit = 5;

	/**
	 * Determine whether a captcha is required in this use case.
	 * Returns true or false depending on the value of
	 * $this->requireCaptcha and the logged in state of the user
	 * @return boolean Whether a captcha is required
	 */
	public function getIsCaptchaRequired() {

		if (!CCaptcha::checkRequirements() || $this->requireCaptcha == "never" || ($this->requireCaptcha == "guests" && !Yii::app()->user->isGuest)) {
			return false;
		}
		return true;
	}

}
