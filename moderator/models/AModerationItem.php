<?php
/**
 * Provides access to items that can be moderated.
 * 
 * @property string $ownerModel the model class that this moderation belongs to
 * @property integer $ownerId the id of the owner model
 * @property integer $moderatorId the id of the moderating user if the vote is not "pending"
 * @property string $status The moderation status, either pending, approved or denied
 * @property string $notes The notes added to the moderation item after moderating
 * @property integer $timeAdded the time this moderation item was added
 * @property integer $timeModerated the time this item was moderated
 * 
 * 
 * @author Charles Pick
 * @package blocks.moderation
 */
class AModerationItem extends CActiveRecord {
	/**
	 * Holds the owner model
	 * @var CActiveRecord
	 */
	protected $_owner;
	/**
	 * The moderation status.
	 * @var string
	 */
	public $status = IAModeratable::PENDING;
	
	/**
	 * The moderation statuses
	 * @var array
	 */
	public static $statuses = array(
			IAModeratable::PENDING => "Pending",
			IAModeratable::APPROVED => "Approved",
			IAModeratable::DENIED => "Denied"
		);
	
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
		return Yii::app()->getModule("moderator")->moderationTable;
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
			array('status', 'required'),
			array('notes','safe'),
			array('status','in','range' => array_keys(self::$statuses)),
			// The following rule is used by search().
			array('ownerModel, ownerId, moderatorId, status', 'safe', 'on'=>'search'),
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
			
		);
	}
	/**
	 * Returns a list of owner models for use in dropdownlists
	 * @return array modelClass => modelClass
	 */
	public static function listOwnerModels() {
		$command = Yii::app()->db->createCommand("SELECT DISTINCT ownerModel FROM ".self::tableName()." ORDER BY ownerModel");
		$listData = array();
		foreach($command->queryAll() as $row) {
			$listData[$row['ownerModel']] = $row['ownerModel'];
		}
		return $listData;
	}
	
	/**
	 * Gets the model that owns this moderation item.
	 * @return CActiveRecord the moderated item
	 */
	public function getOwner() {
		if ($this->_owner === null) {
			$className = $this->ownerModel;
			$this->_owner = $className::model()->findByPk($this->ownerId);
		}
		return $this->_owner;
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('ownerModel',$this->ownerModel,true);
		$criteria->compare('ownerId',$this->ownerId);
		$criteria->compare('moderatorId',$this->moderatorId);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	/**
	 * Runs before the item is saved.
	 * Sets the relevant timestamps if required
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave() {
		if (!isset($_SERVER['REQUEST_TIME'])) {
			$time = time();
		}
		else {
			$time = $_SERVER['REQUEST_TIME'];
		}
		if ($this->isNewRecord) {
			$this->timeAdded = $time;
		}
		if ($this->status === null) {
			$this->status = IAModeratable::PENDING;
		}
		if ($this->status != IAModeratable::PENDING) {
			$this->timeModerated = $time;
		}
		
		return parent::beforeSave();
	}
	/**
	 * Approves this moderation item and saves it.
	 * @return boolean Whether the save succeeded
	 */
	public function approve() {
		$this->status = IAModeratable::APPROVED;
		if (!Yii::app()->user->isGuest) {
			$this->moderatorId = Yii::app()->user->id;
		}
		return $this->save();
	}
	/**
	 * Denies this moderation item and saves it.
	 * @return boolean Whether the save succeeded
	 */
	public function deny() {
		$this->status = IAModeratable::DENIED;
		if (!Yii::app()->user->isGuest) {
			$this->moderatorId = Yii::app()->user->id;
		}
		return $this->save();
	}
	/**
	 * Named Scope: Returns the moderation item owned by the specified model.
	 * @param IAModeratable $owner The owner model
	 * @return AModeration $this The vote model with the scope applied.
	 */
	public function ownedBy($owner) {
		$criteria = new CDbCriteria;
		$criteria->condition = "ownerModel = :moderationOwnerModel AND ownerId = :moderationOwnerId";
		$criteria->params[":moderationOwnerModel"] = $owner->getClassName();
		$criteria->params[":moderationOwnerId"] = $owner->getId();
		
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}
	
}