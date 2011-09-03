<?php
/**
 * AAkismetBehavior provides a wrapper for the Akismet anti blog spam API.
 * To use AAkismetBehavior you must obtain an API key for Akismet, and specify this key
 * in the akismet component configuration ({@link AAkismet::apiKey}).
 * 
 * <b>Basic Usage:</b>
 * 
 * <pre>
 * // assumes $comment is a model with AAkismetBehavior configured
 * if ($comment->isCommentSpam()) { // sends the data to akismet and waits for a response
 * 	$comment->isSpam = true; // flag the comment as spam, but don't delete it so it can be reviewed later
 * }
 * </pre>
 * 
 * <b>Marking an item as spam:</b>
 * 
 * Sometimes a spammy comment makes it through the filter, so when we mark it as spam
 * we should tell akismet about it too, to help improve the spam filter.
 * 
 * <pre>
 * // mark comment as spam
 * $comment->isSpam = true;
 * $comment->submitSpam(); // submits the spammy comment to Akismet
 * </pre>
 * 
 * <b>Marking an item as not spam:</b>
 * 
 * Sometimes a valid comment can get caught in the filter by mistake, in this case we should mark
 * it as "ham" (i.e. not spam)
 * 
 * <pre>
 * // mark comment as not spam
 * $comment->isSpam = false;
 * $comment->submitHam(); // submits the valid content to Akismet
 * </pre>  
 * @package application.modules.admin.components
 * @author Charles Pick
 */
class AAkismetBehavior extends CActiveRecordBehavior {
	
	/**
	 * The blog URL, this is required for akismet. 
	 * Defaults to array("/site/blog")
	 * @see CHtml::normalizeUrl()
	 * @var mixed
	 */
	public $blogUrl = array("/site/blog");
	
	/**
	 * The type of content to send to Akismet, e.g. blog, wiki etc.
	 * Defaults to "comment"
	 * @var string
	 */
	public $type = "comment";
	
	/**
	 * The name of the field on the owner object that should be set to true
	 * when a spam comment is flagged as spam.
	 * Defaults to "isSpam".
	 * @var string
	 */
	public $spamField = "isSpam";
	
	/**
	 * The name of the field on the owner object that contains the author name.
	 * This will be sent to Akismet.
	 * Defaults to "authorName".
	 * @var string
	 */
	public $nameField = "authorName";
	/**
	 * The name of the field on the owner object that contains the author's email address.
	 * This will be sent to Akismet.
	 * Defaults to "authorEmail".
	 * @var string
	 */
	public $emailField = "authorEmail";
	/**
	 * The name of the field on the owner object that contains the URL submitted.
	 * This will be sent to Akismet.
	 * Defaults to "url".
	 * @var string
	 */
	public $urlField = "authorUrl";
	/**
	 * The name of the field on the owner object that contains the comment content.
	 * This will be sent to Akismet.
	 * Defaults to "content".
	 * @var string
	 */
	public $contentField = "content";
	
	/**
	 * Determine whether this owner item is spam or not.
	 * This will send the contents of the comment to Akismet for inspection.
	 * @return boolean true if the comment is spam, otherwise false
	 */
	public function isCommentSpam() {
		if (is_array($this->blogUrl)) {
			$url = $this->blogUrl;
			$url = Yii::app()->controller->createAbsoluteUrl(array_shift($url),$url);
		}
		else {
			$url = $this->blogUrl;
		}
		$akismet = Yii::app()->akismet;
		$akismet->setCommentAuthor($this->owner->{$this->nameField});
		$akismet->setCommentAuthorEmail($this->owner->{$this->emailField});
		$akismet->setCommentAuthorURL($this->owner->{$this->urlField});
		$akismet->setCommentContent($this->owner->{$this->contentField});
		return $akismet->isCommentSpam();
	}
	
	/**
	 * Submit a spam comment to Akismet to help improve the filter
	 */
	public function submitSpam() {
		if (is_array($this->blogUrl)) {
			$url = $this->blogUrl;
			$url = Yii::app()->controller->createAbsoluteUrl(array_shift($url),$url);
		}
		else {
			$url = $this->blogUrl;
		}
		$akismet = Yii::app()->akismet;
		$akismet->setCommentAuthor($this->owner->{$this->nameField});
		$akismet->setCommentAuthorEmail($this->owner->{$this->emailField});
		$akismet->setCommentAuthorURL($this->owner->{$this->urlField});
		$akismet->setCommentContent($this->owner->{$this->contentField});
		$akismet->submitSpam();
	}
	
	/**
	 * Submit a non spam comment to Akismet to help improve the filter
	 */
	public function submitHam() {
		if (is_array($this->blogUrl)) {
			$url = $this->blogUrl;
			$url = Yii::app()->controller->createAbsoluteUrl(array_shift($url),$url);
		}
		else {
			$url = $this->blogUrl;
		}
		$akismet = Yii::app()->akismet;
		$akismet->setCommentAuthor($this->owner->{$this->nameField});
		$akismet->setCommentAuthorEmail($this->owner->{$this->emailField});
		$akismet->setCommentAuthorURL($this->owner->{$this->urlField});
		$akismet->setCommentContent($this->owner->{$this->contentField});
		$akismet->submitHam();
	}
	
	/**
	 * The before save event, checks whether the comment is spam
	 * and if so the field specified in spamField will be set to true.
	 * If no spamField is specified and spam is detected, the save will be halted, otherwise the save will
	 * continue but the spamFlag will be set to true.
	 * @param CEvent $event The before save event
	 * @see CActiveRecordBehavior::beforeSave()
	 */
	public function beforeSave($event) {
		if ($event->sender->isNewRecord && $this->isCommentSpam()) {
			if ($this->spamField === null) {
				$event->isValid = false;
				return false;
			}
			else {
				$event->sender->{$this->spamField} = true;
			}
		}
		elseif ($event->sender->isNewRecord && $this->spamField !== null) {
			$event->sender->{$this->spamField} = false;
		}
		return parent::beforeSave($event);
	}
}
