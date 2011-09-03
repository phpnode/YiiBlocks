<?php

/**
 * ALoginForm is the data structure for keeping user login form data.
 * @author Charles Pick
 * @package packages.users.models
 */
class ALoginForm extends CFormModel {
	/**
	 * The user's email address
	 * @var string
	 */
	public $email;
	/**
	 * The user's password
	 * @var string
	 */
	public $password;
	/**
	 * Whether to allow the user to login automatically or not
	 * @var boolean
	 */
	public $rememberMe;

	/**
	 * The user identity, used when authenticating
	 * @var CUserIdentity
	 */
	protected $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that email and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()	{
		return array(
			array('email, password', 'required'),
			array('email','email'),
			array('rememberMe', 'boolean'),
			array('password', 'authenticate'),
		);
	}

	/**
	 * Declares attribute labels.
	 * @see CFormModel::attributeLabels()
	 * @return array attribute => label
	 */
	public function attributeLabels()
	{
		return array(
			'rememberMe'=>'Remember me next time',
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute, $params) {
		if(!$this->hasErrors()) {
			$identityClass = Yii::app()->getModule("users")->identityClass;
			$this->_identity=new $identityClass($this->email,$this->password);
			if(!$this->_identity->authenticate()) {
				$this->addError('password','Incorrect username or password.');
			}
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login() {
		$usersModule = Yii::app()->getModule("users");
		$identityClass = $usersModule->identityClass;
		if($this->_identity===null)	{
			$this->_identity=new $identityClass($this->email,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===$identityClass::ERROR_NONE) {
			$duration=$this->rememberMe ? $usersModule->autoLoginDuration : 0;
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
	/**
	 * The after construct event, sets the default value of $this->rememberMe
	 * @see CFormModel::afterConstruct()
	 */
	public function afterConstruct() {
		parent::afterConstruct();
		$this->rememberMe = Yii::app()->getModule("users")->autoLoginByDefault;
	}
}
