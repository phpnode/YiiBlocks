<?php

/**
 * Holds information about an authorisation task
 * @author Charles Pick
 * @package packages.rbac.models
 */
class AAuthTask extends AAuthItem
{
	/**
	 * The auth item type
	 * @var integer
	 */
	public $type = self::AUTH_TASK;
	/**
	 * The default scope
	 * @see CActiveRecord::defaultScope()
	 * @return array the scope configuration
	 */
	public function defaultScope() {
		return array(
			"condition" => "type = ".self::AUTH_TASK
		);
	}
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return AAuthTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}