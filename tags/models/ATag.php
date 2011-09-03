<?php

/**
 * This is the model class for table "tags".
 *
 * The followings are the available columns in table 'tags':
 * @property string $tag the name of the tag
 * @property string $slug the url friendly name for the tag
 *
 */
class ATag extends CActiveRecord
{
	public static $_tagList = array();
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
		return Yii::app()->getModule("tags")->tagTableName;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('tag', 'required'),
			array('tag', 'length', 'max'=>50),
			// The following rule is used by search().
			array('tag', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$module = Yii::app()->getModule("tags");
		return array(
			"assignments" => array(self::HAS_MANY,$module->tagModelClass,"tagId"),
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
		$criteria->compare('tag',$this->tag,true);
		$criteria->compare('stub',$this->stub,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}


	/**
	 * Gets a list of top tags for the specified model, along with their weights
	 * @param integer $limit The maximum number of tags to return, defaults to 20
	 * @param Webapp $webapp If specified the top tags for this webapp will be returned, otherwise the site wide top tags will be returned
	 * @return array The tags tag => fontSize
	 */
	public function findTagWeights($limit=20, Webapp $webapp = null)
	{
		$criteria=new CDbCriteria(array(
			'select'=>'tag, weight',
			'group'=>'tag',
			'join' => 'LEFT OUTER JOIN webapptags on webapptags.tagId = t.id',
			'having'=>'weight > 0',
			'order'=>'weight DESC',
			'limit'=>$limit,

		));

		if ($webapp) {
			$criteria->condition = "webappId = :webappId";
			$criteria->params[":webappId"] = $webapp->id;
		}

		$rows=$this->dbConnection->cache(120)->commandBuilder->createFindCommand($this->tableSchema, $criteria)->queryAll();

		$total=0;
		foreach($rows as $row) {
			$total+=$row['weight'];
		}
		$tags=array();
		if($total>0) {
			foreach($rows as $row) {
				$tags[$row['tag']]=8+(int)(16*($row['weight'] )/($total+10));
			}
			ksort($tags);
		}
		return $tags;
	}
}