<?php
/**
 * This is the model class for table "calendarevents".
 *
 * @property string $id the id column in table 'calendarevents'
 * @property string $calendarId the calendarId column in table 'calendarevents'
 * @property string $title the title column in table 'calendarevents'
 * @property string $content the content column in table 'calendarevents'
 * @property string $startsAt the startsAt column in table 'calendarevents'
 * @property string $endsAt the endsAt column in table 'calendarevents'
 * @property boolean $allDay whether this event lasts all day or not
 * @property string $type the type column in table 'calendarevents'
 * @property string $interval the interval column in table 'calendarevents'
 * @property integer $parameters the parameters column in table 'calendarevents'
 * @property string $recurrenceEndsAt the recurrenceEndsAt column in table 'calendarevents'
 *
 * @property Calendars $calendar the calendar relation (ACalendarEvent belongs to Calendars)
 *
 * @package application.models
 */
class ACalendarEvent extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return ACalendarEvent the static model class
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
				'controllerRoute' => '/calendars/event/'
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
		return 'calendarevents';
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
			array('title', 'length', 'max'=>250),
			array('content', 'safe'),
			array('allDay','boolean'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, calendarId, title, content, startsAt, endsAt, type, interval, parameters, recurrenceEndsAt', 'safe', 'on'=>'search'),
		);
	}
	/**
	 * Gets a human friendly summary of the event time
	 * @return string the summary
	 */
	public function getTimeSummary() {
		$startTime = $this->getStartTime();
		$startDate = $this->getStartDate();
		$endTime = $this->getEndTime();
		$endDate = $this->getEndDate();
		if ($endDate == $startDate) {
			if ($this->allDay) {
				return "All day $startDate";
			}
			if ($startTime == $endTime) {
				return $startTime.", ".$startDate;
			}
			return "$startTime - $endTime, $startDate";
		}
		if ($this->allDay) {
			return "$startDate - $endDate";
		}
		return "$startTime, $startDate - $endTime, $endDate";
	}

	/**
	 * Gets the time the event starts
	 * @return string the time the event starts
	 */
	public function getStartTime() {
		$format = Yii::app()->locale->getTimeFormat("short");
		return Yii::app()->dateFormatter->format($format, $this->startsAt);
	}
	/**
	 * Sets the start time
	 * @param string $startTime the start time
	 * @return string the start time
	 */
	public function setStartTime($startTime) {
		if ($this->startsAt == 0) {
			$this->startsAt = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
		}
		$defaults = array(
			"year" => date("Y",$this->startsAt),
			"month" => date("m", $this->startsAt),
			"day" => date("d", $this->startsAt),
		);
		return CDateTimeParser::parse($startTime,Yii::app()->locale->getTimeFormat("short"),$defaults);
	}

	/**
	 * Gets the date the event starts
	 * @return string the date the event starts
	 */
	public function getStartDate() {
		$format = Yii::app()->locale->getDateFormat();
		return Yii::app()->dateFormatter->format($format, $this->startsAt);
	}
	/**
	 * Sets the start date
	 * @param string $startDate the start date
	 * @return string the start date
	 */
	public function setStartDate($startDate) {
		if ($this->startsAt == 0) {
			$this->startsAt = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
		}
		$defaults = array(
			"hour" => date("H",$this->startsAt),
			"minute" => date("i", $this->startsAt),
			"second" => date("s", $this->startsAt),
		);
		return CDateTimeParser::parse($startDate,Yii::app()->locale->getDateFormat(),$defaults);
	}

	/**
	 * Gets the time the event starts
	 * @return string the time the event starts
	 */
	public function getEndTime() {
		$format = Yii::app()->locale->getTimeFormat("short");
		return Yii::app()->dateFormatter->format($format, $this->endsAt);
	}
	/**
	 * Sets the end time
	 * @param string $endTime the end time
	 * @return string the end time
	 */
	public function setEndTime($endTime) {
		if ($this->endsAt == 0) {
			$this->endsAt = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
		}
		$defaults = array(
			"year" => date("Y",$this->endsAt),
			"month" => date("m", $this->endsAt),
			"day" => date("d", $this->endsAt),
		);
		return CDateTimeParser::parse($endTime,Yii::app()->locale->getTimeFormat("short"),$defaults);
	}

	/**
	 * Gets the date the event ends
	 * @return string the date the event ends
	 */
	public function getEndDate() {
		$format = Yii::app()->locale->getDateFormat();
		return Yii::app()->dateFormatter->format($format, $this->endsAt);
	}
	/**
	 * Sets the end date
	 * @param string $endDate the end date
	 * @return string the endate
	 */
	public function setEndDate($endDate) {
		if ($this->endsAt == 0) {
			$this->endsAt = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
		}
		$defaults = array(
			"hour" => date("H",$this->endsAt),
			"minute" => date("i", $this->endsAt),
			"second" => date("s", $this->endsAt),
		);
		return CDateTimeParser::parse($endDate,Yii::app()->locale->getDateFormat(), $defaults);
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
			'calendar' => array(self::BELONGS_TO, 'ACalendar', 'calendarId'),
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
			'calendarId' => 'Calendar',
			'title' => 'Title',
			'content' => 'Content',
			'startsAt' => 'Starts At',
			'endsAt' => 'Ends At',
			'type' => 'Type',
			'interval' => 'Interval',
			'parameters' => 'Parameters',
			'recurrenceEndsAt' => 'Recurrence Ends At',
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
		$criteria->compare('calendarId',$this->calendarId,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('startsAt',$this->startsAt,true);
		$criteria->compare('endsAt',$this->endsAt,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('interval',$this->interval,true);
		$criteria->compare('parameters',$this->parameters);
		$criteria->compare('recurrenceEndsAt',$this->recurrenceEndsAt,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}