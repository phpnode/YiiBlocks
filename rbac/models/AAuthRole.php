<?php

/**
 * Holds information about an authorisation role
 * @author Charles Pick
 * @package packages.rbac.models
 */
class AAuthRole extends AAuthItem
{
	/**
	 * The auth item type
	 * @var integer
	 */
	public $type = self::AUTH_ROLE;
	/**
	 * The default scope
	 * @see CActiveRecord::defaultScope()
	 * @return array the scope configuration
	 */
	public function defaultScope() {
		return array(
			"condition" => "type = ".self::AUTH_ROLE
		);
	}
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return AAuthRole the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}