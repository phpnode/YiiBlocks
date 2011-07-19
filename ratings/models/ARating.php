<?php
/**
 * ARating represents information about a user rating for an item
 * @property integer $id the ID of the rating
 * @property string $ownerModel the model class that this rating belongs to
 * @property integer $ownerId the id of the owner model
 * @property string $raterId the id of the rating user, if they're logged in
 * @property string $raterIP the IP address of the rater, stored in long format
 * @property string $raterUserAgent the user agent of the rater
 * @property integer $score the rating score, 0 to 10
 * @property string $timeAdded the time the rating was added
 * 
 * @author Charles Pick
 * @package blocks.ratings.models
 */
class ARating extends CActiveRecord implements IARating {
	/**
	 * The beforeSave event, sets the rater ip, user agent 
	 * and user id if possible.
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave() {
		if ($this->isNewRecord) {
			if (Yii::app()->getModule("ratings")->requiresLogin) {
				if (Yii::app()->user->isGuest) {
					return false;
				}
				$this->raterId = Yii::app()->user->id;
			}
			$this->raterIP = $_SERVER['REMOTE_ADDR'];
			$this->raterUserAgent = $_SERVER['HTTP_USER_AGENT'];
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
		return Yii::app()->getModule("ratings")->ratingTable;
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
			array('score', 'numerical', 'integerOnly'=>true, 'min' => 0, 'max' => 10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, ownerModel, ownerId, ratingId, ratingIP, ratingUserAgent, score, timeAdded', 'safe', 'on'=>'search'),
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
			'raterId' => 'Rater',
			'raterIP' => 'Rater IP',
			'raterUserAgent' => 'Rater User Agent',
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
		$criteria->compare('ratingId',$this->raterId,true);
		$criteria->compare('ratingIP',$this->raterIP,true);
		$criteria->compare('ratingUserAgent',$this->raterUserAgent,true);
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
		$criteria->condition = "ownerModel = :ratingOwnerModel AND ownerId = :ratingOwnerId";
		$criteria->params[":ratingOwnerModel"] = get_class($owner);
		$criteria->params[":ratingOwnerId"] = $owner->primaryKey;
		
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}
	
	
	/**
	 * Named Scope: Returns all Votes by the current user
	 * @return Vote $this The vote model with the scope applied.
	 */
	public function byCurrentUser() {
		$criteria = new CDbCriteria;
		if (Yii::app()->getModule("ratings")->requiresLogin) {
			$criteria->addCondition("raterId = :raterId");
			$criteria->params[":raterId"] = Yii::app()->user->id;
		}
		else {
			$criteria->addCondition("raterIP = :raterIP AND raterUserAgent = :raterUserAgent");
			$criteria->params[":raterIP"] = $_SERVER['REMOTE_ADDR'];
			$criteria->params[":raterUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
		}
		
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}
}
