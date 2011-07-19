<?php

/**
 * Represents information about a vote.
 *
 * @property string $id the vote id
 * @property string $ownerModel the model class that this vote belongs to
 * @property string $ownerId the id of the owner model
 * @property string $voterId the id of the voting user, if they're logged in
 * @property string $voterIP the IP address of the voter, stored in long format
 * @property string $voterUserAgent the user agent of the voter
 * @property integer $score the vote score, either +1 or -1
 * @property string $timeAdded the time the vote was added
 * @author Charles Pick
 * @package blocks.voting.models
 */
class AVote extends CActiveRecord implements IAVote {
	
	/**
	 * The beforeSave event, sets the voter ip, user agent 
	 * and user id if possible.
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave() {
		if ($this->isNewRecord) {
			if (Yii::app()->getModule("voting")->requiresLogin) {
				if (Yii::app()->user->isGuest) {
					return false;
				}
				$this->voterId = Yii::app()->user->id;
			}
			$this->voterIP = $_SERVER['REMOTE_ADDR'];
			$this->voterUserAgent = $_SERVER['HTTP_USER_AGENT'];
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
		return Yii::app()->getModule("voting")->voteTable;
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
			array('score', 'required'),
			array('score', 'numerical', 'integerOnly'=>true, 'min' => -1, 'max' => 1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, ownerModel, ownerId, voterId, voterIP, voterUserAgent, score, timeAdded', 'safe', 'on'=>'search'),
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
			'voterId' => 'Voter',
			'voterIP' => 'Voter Ip',
			'voterUserAgent' => 'Voter User Agent',
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
		$criteria->compare('voterId',$this->voterId,true);
		$criteria->compare('voterIP',$this->voterIP,true);
		$criteria->compare('voterUserAgent',$this->voterUserAgent,true);
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
	 * Named Scope: Returns all Votes by the current user
	 * @return Vote $this The vote model with the scope applied.
	 */
	public function byCurrentUser() {
		$criteria = new CDbCriteria;
		if (Yii::app()->voteManager->requiresLogin) {
			$criteria->addCondition("voterId = :voterId");
			$criteria->params[":voterId"] = Yii::app()->user->id;
		}
		else {
			$criteria->addCondition("voterIP = :voterIP AND voterUserAgent = :voterUserAgent");
			$criteria->params[":voterIP"] = $_SERVER['REMOTE_ADDR'];
			$criteria->params[":voterUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
		}
		
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}
	
	/**
	 * err
	 */
	public static function userHasVotedRelation($ownerModel) {
		$relation = array(
			CActiveRecord::STAT,
			"Vote",
			"ownerId",
		);
		if (Yii::app()->voteManager->requiresLogin) {
			$relation['condition'] = "voterId = :voterId AND ownerModel = :ownerModel";
			$relation['params'] = array(
				":voterId" => Yii::app()->user->id,
				":ownerModel" => $ownerModel,
				);
		}
		else {
			$relation['condition'] = "voterIP = :voterIP AND voterUserAgent = :voterUserAgent AND ownerModel = :ownerModel";
			$relation['params'] = array(
					":voterIP" => $_SERVER['REMOTE_ADDR'],
					":voterUserAgent" => $_SERVER['HTTP_USER_AGENT'],
					":ownerModel" => $ownerModel,
				);
		}
		
		return $relation;
	}
}