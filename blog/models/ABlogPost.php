<?php

/**
 * This is the model class for table "posts".
 *
 * @property string $id the id column in table 'posts'
 * @property string $title the title column in table 'posts'
 * @property string $slug the slug column in table 'posts'
 * @property string $description the description column in table 'posts'
 * @property string $summary the summary column in table 'posts'
 * @property string $content the content column in table 'posts'
 * @property string $authorId the authorId column in table 'posts'
 * @property string $status the status for this post, either draft, published or archived
 * @property string $timePublished the timePublished column in table 'posts'
 * @property string $timeAdded the timeAdded column in table 'posts'
 *
 * @property Users $author the author relation (BlogPost belongs to Users)
 *
 * @package application.models
 */
class ABlogPost extends CActiveRecord {

	/**
	 * Whether the current user has voted for this item or not.
	 * @see AVotable::withVoteInfo()
	 * @var boolean
	 */
	public $_userHasVoted;

	/**
	 * Contains the vote score if the current user has voted.
	 * Usually -1 or 1.
	 * @see AVotable::withVoteInfo()
	 * @var integer
	 */
	public $_userVoteScore;

	/**
	 * Contains the total number of votes for this item.
	 * @see AVotable::withVoteInfo()
	 * @var integer
	 */
	public $_totalVotes;

	/**
	 * Contains the total score for this item.
	 * @see AVotable::withVoteInfo()
	 * @var integer
	 */
	public $_totalVoteScore;

	/**
	 * Gets the custom behaviors for this model
	 * @see CActiveRecord::behaviors()
	 * @return array The behavior configuration
	 */
	public function behaviors() {
		return array(
			"AVotable" => array(
				"class" => "packages.voting.components.AVotable"
			),
			'ATaggable' => array(
				'class' => 'packages.tags.components.ATaggable',
			),
//			"commentable" => array(
//				"class" => "ACommentable",
//			),
			"ALinkable" => array(
				"class" => "packages.linkable.ALinkable",
				"template" => "{title}",
				"attributes" => "slug",
				"controllerRoute" => "/blog/post/",
			),
			"ASluggable" => array(
				"class" => "packages.slug.ASluggable",
				"slugTemplate" => "{title}",
			),
			"AFeedable" => array(
				"class" => "packages.feeds.AFeedable",

			),

		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return ABlogPost the static model class
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
		return Yii::app()->getModule("blog")->postTable;
	}

	/**
	 * Returns the validation rules for attributes.
	 * @see CModel::rules()
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title', 'required'),
			array('description, summary, content', 'required', 'on' => 'publish, archive'),
			array('status', 'in', 'range' => array('draft','published','archived')),
			array('title', 'length', 'max'=>150),
			array('description', 'length', 'max'=>250),
			array('summary', 'length', 'max'=>1000),
			array('content,tags', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('title, status, timeAdded', 'safe', 'on'=>'search'),
		);
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
			'author' => array(self::BELONGS_TO, 'User', 'authorId'),
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
			'title' => 'Title',
			'slug' => 'Slug',
			'description' => 'Description',
			'summary' => 'Summary',
			'content' => 'Content',
			'authorId' => 'Author',
			'status' => 'Status',
			'timePublished' => 'Time Published',
			'timeAdded' => 'Time Created',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		$criteria=new CDbCriteria;
		if ($this->timeAdded != "" && !is_numeric($this->timeAdded)) {
			if (substr($this->timeAdded,0,1) == ">" || substr($this->timeAdded,0,1) == "<") {
				if (substr($this->timeAdded,1,1) == "=") {
					$timeAdded = substr($this->timeAdded,0,2).strtotime(substr($this->timeAdded,2));
				}
				else {
					$timeAdded = substr($this->timeAdded,0,1).strtotime(substr($this->timeAdded,1));
				}
			}
			else {
				$timeAdded = strtotime($this->timeAdded);
			}
		}
		else {
			$timeAdded = $this->timeAdded;
		}
		$criteria->compare('title',$this->title,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('timeAdded',$timeAdded);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	/**
	 * Sets the timeAdded, authorId, timePublished etc if required
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave() {
		if (isset($_SERVER['REQUEST_TIME'])) {
			$time = $_SERVER['REQUEST_TIME'];
		}
		else {
			$time = time();
		}
		if ($this->isNewRecord) {
			$this->timeAdded = $time;
			if (!Yii::app()->user->isGuest) {
				$this->authorId = Yii::app()->user->id;
			}
		}
		if ($this->status == "published" && $this->timePublished == 0) {
			if ($this->publish() === false) {
				return false;
			}
		}

		return parent::beforeSave();
	}
	/**
	 * Publishes the blog post, but does not save the changes.
	 * Usage:
	 * <pre>
	 * $model->publish()->save();
	 * </pre>
	 * @return ABlogPost $this published blog post or false if publishing failed
	 */
	public function publish() {
		if ($this->beforePublish()) {
			$this->status = "published";
			$this->timePublished = (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
			$this->afterPublish();
			return $this;
		}
		else {
			return false;
		}
	}

	/**
	 * This method is invoked before publishing a post
	 * The default implementation raises the {@link onBeforePublish} event.
	 * You may override this method to do any preparation work for publishing	 *
	 * Make sure you call the parent implementation so that the event is raised properly.
	 * @return boolean whether the post should be published. Defaults to true.
	 */
	protected function beforePublish()
	{
		if($this->hasEventHandler('onBeforePublish'))
		{
			$event=new CModelEvent($this);
			$this->onBeforePublish($event);
			return $event->isValid;
		}
		else {
			return true;
		}
	}

	/**
	 * This method is invoked after a post is published
	 * The default implementation raises the {@link onAfterPublish} event.
	 * You may override this method to do postprocessing after publishing.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 */
	protected function afterPublish()
	{
		if($this->hasEventHandler('onAfterPublish')) {
			$this->onAfterPublish(new CEvent($this));
		}
	}

	/**
	 * This event is raised before the post is published
	 * @param CEvent $event the event parameter
	 * @since 1.0.2
	 */
	public function onBeforePublish($event)
	{
		$this->raiseEvent('onBeforePublish',$event);
	}


	/**
	 * This event is raised after the post is published
	 * @param CEvent $event the event parameter
	 * @since 1.0.2
	 */
	public function onAfterPublish($event)
	{
		$this->raiseEvent('onAfterPublish',$event);
	}

	/**
	 * Named scope: Finds published posts
	 * @return ABlogPost $this with the scope applied
	 */
	public function published() {
		$criteria = new CDbCriteria;
		$criteria->condition = "t.status = 'published'";
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Named scope: Finds draft posts
	 * @return ABlogPost $this with the scope applied
	 */
	public function draft() {
		$criteria = new CDbCriteria;
		$criteria->condition = "t.status = 'draft'";
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Named scope: Finds archived posts
	 * @return ABlogPost $this with the scope applied
	 */
	public function archived() {
		$criteria = new CDbCriteria;
		$criteria->condition = "t.status = 'archived'";
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Named scope: Finds the newest posts first
	 * @return ABlogPost $this with the scope applied
	 */
	public function newestFirst() {
		$criteria = new CDbCriteria;
		$criteria->order = "t.timePublished DESC";
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}
}