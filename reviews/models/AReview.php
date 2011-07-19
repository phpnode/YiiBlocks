<?php

Yii::import("blocks.voting.interfaces.IAVote");

/**
 * A base model for user provided reviews.
 * Reviews can be attached to other models using the AReviewable behavior.
 * 
 * 
 * @property string $id the vote id
 * @property string $ownerModel the model class that this vote belongs to
 * @property string $ownerId the id of the owner model
 * @property string $reviewerId the id of the voting user, if they're logged in
 * @property string $reviewerIP the IP address of the reviewer, stored in long format
 * @property string $reviewerUserAgent the user agent of the reviewer
 * @property integer $score the vote score, either +1 or -1
 * @property string $timeAdded the time the vote was added
 * 
 * 
 * @author Charles Pick
 * @package blocks.reviews
 */
class AReview extends CActiveRecord implements IARating, IAModeratable {
	
	/**
	 * The behaviors attached to this model.
	 * Child classes that override this method should call the
	 * parent implementation.
	 * @see CActiveRecord::behaviors()
	 * @return array The behaviors to attach 
	 */
	public function behaviors() {
		$behaviors = array(
			
			"decorator" => array(
					"class" => "blocks.decorating.AModelDecorator",
					"decoratorPath" => "blocks.reviews.views.review",
				),
			"votable" => array(
					"class" => "blocks.voting.components.AVotable",
				),
			"ownable" => array(
					"class" => "blocks.ownable.AOwnable",
					"attribute" => "reviewer",
					"keyAttribute" => "reviewerId",
					"ownerClassName" => "User"
				),
			"linkable" => array(
					"class" => "blocks.linkable.ALinkable"
				),
			
		);
		if (Yii::app()->getModule("reviews")->moderateReviews) {
			$behaviors["moderatable"] = array(
					"class" => "blocks.moderator.components.AModeratable",
				);
		}
		return $behaviors;
	}
	
	/**
	 * Gets the id of the object being moderated.
	 * @return integer the id of the object being moderated.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Gets the name of the class that is being moderated.
	 * @return string the owner model class name
	 */
	public function getClassName() {
		return __CLASS__;
	}
	
	/**
	 * Whether this particular object should be moderated.
	 * @return boolean true if the object should be moderated
	 */
	public function isModeratable() {
		return true;
	}
	
	
	/**
	 * The beforeSave event, sets the reviewer ip, user agent 
	 * and user id if possible.
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave() {
		if ($this->isNewRecord) {
			if (Yii::app()->user->isGuest) {
				if (Yii::app()->getModule("reviews")->requiresLogin) {
					return false;
				}
			}
			else {
				$this->reviewerId = Yii::app()->user->id;
			}
			$this->reviewerIP = $_SERVER['REMOTE_ADDR'];
			$this->reviewerUserAgent = $_SERVER['HTTP_USER_AGENT'];
			$this->timeAdded = (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
		}
		return parent::beforeSave();
	}
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return Vote the static model class
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
		return 'reviews';
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
			array('score,title,content', 'required'),
			array('title','length','max' => 150),
			array('score', 'numerical', 'integerOnly'=>true, 'min' => 0, 'max' => 10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, ownerModel, ownerId, reviewerId, reviewerIP, reviewerUserAgent, score, timeAdded', 'safe', 'on'=>'search'),
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
			'reviewerId' => 'Voter',
			'reviewerIP' => 'Voter Ip',
			'reviewerUserAgent' => 'Voter User Agent',
			'score' => 'Score',
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
		$criteria->compare('reviewerId',$this->reviewerId,true);
		$criteria->compare('reviewerIP',$this->reviewerIP,true);
		$criteria->compare('reviewerUserAgent',$this->reviewerUserAgent,true);
		$criteria->compare('score',$this->score);
		$criteria->compare('timeAdded',$this->timeAdded,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	/**
	 * Named Scope: Returns all Votes owned by the specified model.
	 * @param CActiveRecord $owner The owner model
	 * @return Vote $this The vote model with the scope applied.
	 */
	public function ownedBy(CActiveRecord $owner) {
		$criteria = new CDbCriteria;
		$criteria->condition = "ownerModel = :voteOwnerModel AND ownerId = :voteOwnerId";
		$criteria->params[":voteOwnerModel"] = get_class($owner);
		$criteria->params[":voteOwnerId"] = $owner->primaryKey;
		
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}
	
	
	/**
	 * Named Scope: Returns all reviews by the current user
	 * @return AReview $this The review model with the scope applied.
	 */
	public function byCurrentUser() {
		$criteria = new CDbCriteria;
		if (Yii::app()->getModule("reviews")->requiresLogin) {
			$criteria->addCondition("reviewerId = :reviewerId");
			$criteria->params[":reviewerId"] = Yii::app()->user->id;
		}
		else {
			$criteria->addCondition("reviewerIP = :reviewerIP AND reviewerUserAgent = :reviewerUserAgent");
			$criteria->params[":reviewerIP"] = $_SERVER['REMOTE_ADDR'];
			$criteria->params[":reviewerUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
		}
		
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}
	
	
}