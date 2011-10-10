<?php

/**
 * This is the model class for table "comments".
 *
 * @property integer $id the id of the comment
 * @property string $ownerModel the class of the model that owns this comment
 * @property string $ownerId the id of the owner model
 * @property integer $replyToId the id of the comment this is a reply to
 * @property string $authorId the id of the author
 * @property string $authorName the name of the author
 * @property string $authorEmail the author's email address
 * @property string $authorUrl the user supplied URL
 * @property string $authorIP the (long) ip of the author
 * @property string $authorUserAgent the user agent string for the comment author
 * @property string $content the comment content
 * @property boolean $isApproved whether the comment is approved or not
 * @property boolean $isSpam whether the comment is flagged as spam or not
 * @property string $timeAdded the time the comment was posted
 *
 * @package application.modules.admin.models
 */
class AComment extends CActiveRecord
{

	/**
	 * The captcha string
	 * @var string
	 */
	public $verifyCode;

	/**
	 * Holds the owner object
	 * @var CActiveRecord
	 */
	protected $_owner;

	/**
	 * Holds the replies
	 * @var AComment[]
	 */
	protected $_replies;

	/**
	 * The comment behaviors.
	 * @see CActiveRecord::behaviors()
	 */
	public function behaviors() {
		$behaviors = array();

		if (Yii::app()->getModule("comments")->votableComments) {
			$behaviors["AVotable"] = array(
				"class" => "packages.voting.components.AVotable",
			);
		}
		if (Yii::app()->getModule("comments")->useAkismet) {
			$behaviors["AAkismet"] = array(
				"class" => "packages.akismet.AAkismetBehavior",
			);
		}
		if (Yii::app()->getModule("comments")->useNestedSet) {
			$behaviors["ENestedSet"] = array(
					"class" => "packages.nestedSet.ENestedSetBehavior",
					"hasManyRoots" => true,
					"rootAttribute" => "root",
					"leftAttribute" => "lft",
					"rightAttribute" => "rgt",
					"levelAttribute" => "level",
				);
		}
		return $behaviors;
	}

	/**
	 * Gets the object that owns this comment
	 * @return CActiveRecord The active record that owns this comment
	 */
	public function getOwner() {
		if ($this->_owner === null) {
			$this->_owner = new $this->ownerModel;
			$this->_owner = $this->_owner->findByPK($this->ownerId);
		}
		return $this->_owner;
	}

	/**
	 * The beforeSave event, sets the authorId if possible etc
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave() {
		if ($this->isNewRecord) {
			if (!Yii::app()->user->isGuest) {
				$this->authorName = Yii::app()->user->name;
				$this->authorId = Yii::app()->user->id;
			}
			$this->authorIP = $_SERVER['REMOTE_ADDR'];
			$this->authorUserAgent = $_SERVER['HTTP_USER_AGENT'];
			$this->timeAdded = (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
			if (!$this->beforePost()) {
				return false;
			}
		}
		return parent::beforeSave();
	}
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return AComment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Returns the name of the associated database table.
	 * @see CActiveRecord::tableName()
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return Yii::app()->getModule("comments")->commentTable;
	}

	/**
	 * Returns the validation rules for attributes.
	 * @see CModel::rules()
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		if (Yii::app()->user->isGuest) {
			$required = "authorName, authorEmail, content";
		}
		else {
			$required = "content";
		}
		return array(
			array($required, 'required'),
			array('authorName', 'length', 'max'=>50),
			array('authorEmail', 'length', 'max'=>450),
			array('authorEmail', 'email'),
			array('authorUrl', 'url'),
			// verifyCode needs to be entered correctly
			array('verifyCode', 'captcha', 'captchaAction' => Yii::app()->getModule("comments")->captchaAction, 'allowEmpty'=>(!Yii::app()->getModule("comments")->isCaptchaRequired)),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, ownerModel, ownerId, authorId, authorName, authorEmail, content, isApproved, timeAdded', 'safe', 'on'=>'search'),
		);

	}
	/**
	 * Adds a reply to this comment.
	 * @param AComment $reply The reply to add
	 * @param boolean $runValidation Whether to run validation or not, defaults to true.
	 * @return boolean Whether the save succeeded
	 */
	public function addReply(AComment $reply, $runValidation = true) {
		$reply->ownerModel = $this->ownerModel;
		$reply->ownerId = $this->ownerId;
		$reply->replyToId = $this->id;

		if (Yii::app()->getModule("comments")->useNestedSet) {
			// nested set behaves slightly differently
			return $reply->appendTo($this,$runValidation);
		}
		else {
			return $reply->save($runValidation);
		}
	}

	/**
	 * Gets the replies for this comment
	 * @return AComment[] the replies
	 */
	public function getReplies() {
		if ($this->_replies === null) {
			$manager = Yii::app()->getModule("comments");
			if ($manager->useNestedSet) {
				$this->_replies = array();
				$nodes = $this->descendants()->findAll();
				$stack = array();
				$l = 0;
				foreach($nodes as $node) {
					$l = count($stack);

					// are these different levels?

					while($l > 0 && $stack[$l - 1]->level >= $node->level) {
						array_pop($stack);
						$l--;
					}
					if ($l == 0) {
						// stack is empty
						$i = count($this->_replies);
						$this->_replies[$i] = $node;
						$stack[] =& $this->_replies[$i];
					}
					else {
						// add to parent
						$i = count($stack[$l - 1]->_replies);
						$stack[$l - 1]->_replies[$i] = $node;
						$stack[] =& $stack[$l - 1]->_replies[$i];
					}
				}
			}
			else {
				$criteria = new CDbCriteria;
				$criteria->condition = "replyToId = :replyToId";
				$criteria->params[":replyToId"] = $this->id;
				$this->_replies = self::model()->findAll($criteria);
			}
		}
		return $this->_replies;
	}

	/**
	 * Sets the replies for this comment.
	 * @param AComment[] $replies The replies
	 */
	public function setReplies($replies) {
		$this->_replies = $replies;
	}

	/**
	 * Named Scope: Find comments that belong to the specified object
	 * @param CActiveRecord $owner The owner object
	 * @return AComment $this with the scope applied
	 */
	public function ownedBy(CActiveRecord $owner) {
		$criteria = new CDbCriteria;
		$criteria->condition = "t.ownerModel = :ownerModel AND t.ownerId = :ownerId";
		$criteria->params[":ownerModel"] = get_class($owner);
		$criteria->params[":ownerId"] = $owner->primaryKey;
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}
	/**
	 * Named Scope: Find approved comments
	 * @return AComment $this with the scope applied
	 */
	public function approved() {
		$criteria = new CDbCriteria();
		$criteria->condition = "t.isApproved = 1";
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Named Scope: Find unapproved comments
	 * @return AComment $this with the scope applied
	 */
	public function unapproved() {
		$criteria = new CDbCriteria();
		$criteria->condition = "t.isApproved = 0";
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}
	/**
	 * Named Scope: Return the newest comments first
	 * @return AComment $this with the scope applied
	 */
	public function newestFirst() {
		$criteria = new CDbCriteria();
		$criteria->order = "t.id DESC";
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Named Scope: Return root comments.
	 * @return AComment $this with the scope applied
	 */
	public function rootComments() {
		if (Yii::app()->getModule("comments")->useNestedSet) {
			return $this->roots();
		}
		else {
			$criteria = new CDbCriteria();
			$criteria->condition = "t.parentId IS NULL";
			$this->getDbCriteria()->mergeWith($criteria);
			return $this;
		}
	}

	/**
	 * Returns the relational rules that specify the relations this model uses
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * Returns the attribute labels. Attribute labels are mainly used in error messages of validation.
	 * @see CModel::attributeLabels()
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ownerModel' => 'Owner Model',
			'ownerId' => 'Owner',
			'authorId' => 'Author',
			'authorName' => 'Name',
			'authorEmail' => 'Email',
			'content' => 'Comment',
			'isApproved' => 'Is Approved',
			'isSpam' => 'Is Spam',
			'timeAdded' => 'Time Added',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('ownerModel',$this->ownerModel,true);
		$criteria->compare('ownerId',$this->ownerId,true);
		$criteria->compare('authorId',$this->authorId,true);
		$criteria->compare('authorName',$this->authorName,true);
		$criteria->compare('authorEmail',$this->authorEmail,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('isApproved',$this->isApproved);
		$criteria->compare('timeAdded',$this->timeAdded,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * This method is invoked before a comment is posted
	 * The default implementation raises the {@link onBeforePost} event.
	 * You may override this method to do any preparation work for comment posting.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 * @return boolean whether the comment can be posted, defaults to true
	 */
	protected function beforePost() {
		if($this->hasEventHandler('onBeforePost'))
		{
			$event=new CModelEvent($this);
			$this->onBeforePost($event);
			return $event->isValid;
		}
		else
			return true;
	}

	/**
	 * This event is raised before a comment is posted
	 * By setting {@link CModelEvent::isValid} to be false, the normal {@link post()} process will be stopped.
	 * @param CModelEvent $event the event parameter
	 */
	public function onBeforePost($event) {
		$this->raiseEvent('onBeforePost',$event);
	}

	/**
	 * This method is invoked after a comment is posted
	 * The default implementation raises the {@link onAfterPost} event.
	 * You may override this method to do postprocessing after comment posting.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 */
	protected function afterPost() {
		if($this->hasEventHandler('onAfterPost'))
			$this->onAfterPost(new CEvent($this));
	}

	/**
	 * This event is raised after the comment is posted
	 * @param CEvent $event the event parameter
	 */
	public function onAfterPost($event)	{
		$this->raiseEvent('onAfterPost',$event);
	}

	/**
	 * This method is invoked before a reply is posted
	 * The default implementation raises the {@link onBeforePost} event.
	 * You may override this method to do any preparation work for comment posting.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 * @return boolean whether the reply can be posted, defaults to true
	 */
	protected function beforeReply() {
		if($this->hasEventHandler('onBeforeReply'))
		{
			$event=new CModelEvent($this);
			$this->onBeforeReply($event);
			return $event->isValid;
		}
		else
			return true;
	}

	/**
	 * This event is raised before a reply is posted
	 * By setting {@link CModelEvent::isValid} to be false, the normal {@link post()} process will be stopped.
	 * @param CModelEvent $event the event parameter
	 */
	public function onBeforeReply($event) {
		$this->raiseEvent('onBeforeReply',$event);
	}

	/**
	 * This method is invoked after a reply is posted
	 * The default implementation raises the {@link onAfterReply} event.
	 * You may override this method to do postprocessing after reply posting.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 */
	protected function afterReply() {
		if($this->hasEventHandler('onAfterReply'))
			$this->onAfterReply(new CEvent($this));
	}

	/**
	 * This event is raised after the reply is posted
	 * @param CEvent $event the event parameter
	 */
	public function onAfterReply($event)	{
		$this->raiseEvent('onAfterReply',$event);
	}
}