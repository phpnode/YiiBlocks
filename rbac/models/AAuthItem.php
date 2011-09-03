<?php

/**
 * This is the model class for table "AuthItem".
 *
 * @property string $name the name column in table 'AuthItem'
 * @property integer $type the type column in table 'AuthItem'
 * @property string $description the description column in table 'AuthItem'
 * @property string $bizrule the bizrule column in table 'AuthItem'
 * @property string $data the data column in table 'AuthItem'
 *
 * @property AuthAssignment[] $authAssignments the authAssignments relation (AAuthItem has many AuthAssignment)
 * @property AuthItemChild[] $authItemChildren the authItemChildren relation (AAuthItem has many AuthItemChild)
 * @property AuthItemChild[] $authItemChildren1 the authItemChildren1 relation (AAuthItem has many AuthItemChild)
 *
 * @package packages.rbac.models
 */
class AAuthItem extends CActiveRecord
{
	const AUTH_OPERATION = 0;
	const AUTH_TASK = 1;
	const AUTH_ROLE = 2;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return AAuthItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	/**
	 * Declares the behaviors to attach to this model
	 * @return array the config for the behaviors to attach to the model
	*/
	public function behaviors() {
		return array(
			"ASluggable" => array(
				"class" => "packages.slug.ASluggable",
				"slugTemplate" => "{name}",
			),
			"ALinkable" => array(
				"class" => "packages.linkable.ALinkable",
				"template" => "{name}",
				"attributes" => array("slug"),
				"controllerRoute" => strtolower(substr(get_class($this),5)),
			)
		);
	}
	/**
	 * Creates an AAuthItem instance.
	 * This method returns an instance of either AAuthOperation, AAuthRole or AAuthTask depending on the
	 * value of the type attribute.
	 * @param array $attributes list of attribute values for the active records.
	 * @return AAuthItem the active record
	 */
	protected function instantiate($attributes)
	{
		switch($attributes['type']) {
			case self::AUTH_TASK:
				$class = "AAuthTask";
				break;
			case self::AUTH_OPERATION:
				$class = "AAuthOperation";
				break;
			case self::AUTH_ROLE:
				$class = "AAuthRole";
				break;
			default:
				$class = get_class($this);
				break;
		}
		$model=new $class(null);
		return $model;
	}
	/**
	 * Returns the name of the associated database table.
	 * @see CActiveRecord::tableName()
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'AuthItem';
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
			array('name, type', 'required'),
			array('name','unique'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>64),
			array('description, bizrule, data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('name, type, description, bizrule, data', 'safe', 'on'=>'search'),
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
			'authAssignments' => array(self::HAS_MANY, 'AuthAssignment', 'itemname'),
			'authItemChildren' => array(self::HAS_MANY, 'AuthItemChild', 'parent'),
			'authItemChildren1' => array(self::HAS_MANY, 'AuthItemChild', 'child'),
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
			'name' => 'Name',
			'type' => 'Type',
			'description' => 'Description',
			'bizrule' => 'Bizrule',
			'data' => 'Data',
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

		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('bizrule',$this->bizrule,true);
		$criteria->compare('data',$this->data,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	/**
	 * Formats the business rule with PHP syntax highlighting
	 * @return string the highlighted business rule
	 */
	public function formatBizRule() {
		$phpString = highlight_string("<?php ".$this->bizrule,true);
		return "<code>".substr($phpString,strpos($phpString,"</span>"));
	}
	/**
	 * Gets the child authorisation items for this item
	 * @param integer $type The item type to return, if not specified children of all types will be returned
	 * @return AAuthItem[] the item children
	 */
	public function getChildren($type = null) {
		if ($type === null) {
			return Yii::app()->authManager->getItemChildren($this->name);
		}
		$items = array();
		foreach(Yii::app()->authManager->getItemChildren($this->name) as $name => $item) {
			if ($item->type == $type) {
				$items[$name] = $item;
			}
		}
		return $items;

	}
	/**
	 * Gets a summary of the access rights for this item
	 * @return string the access summary html
	 */
	public function getSummary() {
		$summary = array();
		$all = $this->getChildren();
		if (!count($all)) {
			return "<li>".$this->description."<br /></li>";
		}
		foreach($all as $item) {
			if ($item->type == self::AUTH_OPERATION) {
				$line = "<li>".$item->description."<br /></li>";
			}
			else {

				$children = Yii::app()->authManager->getItemChildren($item->name);
				if (count($children)) {
					$line = "<li>".$item->description."<br /><ul>";
					foreach($children as $child) {
						$line .= AAuthItem::model()->findByPk($child->name)->getSummary();

					}
					$line .= "</ul>";
				}
				else {
					$line = "<li>".$item->description."<br />";
				}
				$line .= "</li>";
			}
			$summary[] = $line;
		}
		return "<li>".$this->description."<br /><ul style='padding-left:10px;'>".implode("\n",$summary)."</ul><br /></li>";
	}


}