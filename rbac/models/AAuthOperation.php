<?php

/**
 * Holds information about an authorisation operation
 * @author Charles Pick
 * @package packages.rbac.models
 */
class AAuthOperation extends AAuthItem
{
	/**
	 * The auth item type
	 * @var integer
	 */
	public $type = self::AUTH_OPERATION;
	/**
	 * The default scope
	 * @see CActiveRecord::defaultScope()
	 * @return array the scope configuration
	 */
	public function defaultScope() {
		return array(
			"condition" => "type = ".self::AUTH_OPERATION
		);
	}
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return AAuthOperation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}