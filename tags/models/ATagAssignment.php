<?php

/**
 * This is the model class for table "tagassignments".
 *
 * @property string $ownerModel the name of the model that owns this assignment
 * @property integer $ownerId the id of the model that owns this assignment
 * @property integer $tagId the id of the tag
 * @property integer $userId the id of the user who made this assignment
 * @property integer $timeAdded the time the assignment was added
 *
 */
class ATagAssignment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ATag the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return Yii::app()->getModule("tags")->tagAssignmentTableName;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(

		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$module = Yii::app()->getModule("tags");
		return array(
			"tag" => array(self::BELONGS_TO,$module->tagModelClass,"tagId"),
		);
	}

}