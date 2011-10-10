<?php
/**
 * This is the model class for table "calendars".
 *
 * @property string $id the id column in table 'calendars'
 * @property string $name the name column in table 'calendars'
 * @property string $description the description column in table 'calendars'
 * @property string $userId the userId column in table 'calendars'
 *
 * @property ACalendarEvent[] $events the events relation (ACalendar has many ACalendarEvent)
 * @property Users $user the user relation (ACalendar belongs to Users)
 *
 * @package application.models
 */
class ACalendar extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return ACalendar the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	/**
	 * The calendar behaviors.
	 * @see CActiveRecord::behaviors()
	 * @return array the behaviors to attach
	 */
	public function behaviors() {
		return array(
			"ALinkable" => array(
				"class" => "packages.linkable.ALinkable",
				'controllerRoute' => '/calendars/calendar/'
			)
		);
	}
	/**
	 * Returns the name of the associated database table.
	 * @see CActiveRecord::tableName()
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'calendars';
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
			array('name', 'required'),
			array('description', 'safe'),
			array('name', 'length', 'max'=>150),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, description, userId', 'safe', 'on'=>'search'),
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
			'events' => array(self::HAS_MANY, 'ACalendarEvent', 'calendarId'),
			'user' => array(self::BELONGS_TO, 'User', 'userId'),
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
			'name' => 'Name',
			'description' => 'Description',
			'userId' => 'User',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('userId',$this->userId,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function addEvent(ACalendarEvent $event) {

	}
}