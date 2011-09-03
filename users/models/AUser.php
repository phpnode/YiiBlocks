<?php
/**
 * The user model.
 * @package packages.users.models 
 */
abstract class AUser extends CActiveRecord {
	/**
	 * Holds the user's password
	 * @see beforeSave()
	 * @see afterFind()
	 * @var string
	 */
	protected $_password;
	/**
	 * The default validation rules.
	 * Child classes that specify more rules should merge with
	 * the parent implementation, e.g.
	 * <pre>
	 * public function rules() {
	 * 	return CMap::mergeArray(parent::rules(),array(
	 * 		// custom rules go here... 
	 * ));
	 * }
	 * </pre>
	 */
	public function rules() {
		return array(
			array("name,email,password","required","on" => "register"),
			array("email","email"),
			array("password","length","min" => 6),
			array("email","required", "on" => "resetPassword"),
			array("password","required", "on" => "newPassword"),
		);
	}
	/**
	 * Generates a password reset code for this user
	 * @return string the password reset code for this user
	 */
	public function getPasswordResetCode() {
		return sha1("ResetPassword:".$this->id.$this->salt.".".$this->password);
	}
	
	/**
	 * Generates an activation code for this user
	 * @return string the activation code for this user
	 */
	public function getActivationCode() {
		return sha1("Activate:".$this->id.$this->salt.".".$this->password);
	}
	
	/**
	 * Generates a unique salt for this user.
	 * @return string the unique salt
	 */
	protected function generateSalt() {
		return sha1(uniqid());
	}
	
	/**
	 * Hashes a password.
	 * @param string $password The plain text password to hash
	 * @param string $salt The salt to use for hashing
	 * @param integer $rounds The number of hashing rounds to perform, defaults to 1000
	 * @return string The hashed password
	 */
	protected function hashPassword($password, $salt, $rounds = 10000) {
		for($i = 0; $i < $rounds; $i++) {
			$hash = sha1($salt.$password.$salt);
		}
		return $hash;
	}
	/**
	 * Compares the given password to the stored password for this user
	 * @param string $password The password to check
	 * @return boolean true if the password matches
	 */
	public function verifyPassword($password) {
		Yii::beginProfile("VerifyPassword");
		$result = (self::hashPassword($password,$this->salt) == $this->password);
		Yii::endProfile("VerifyPassword");
		return $result;
	}
	
	/**
	 * Invoked after a user model is saved.
	 * Invokes beforeRegister() and hashes the user's password if required.
	 * @see CActiveRecord::beforeSave()
	 * @see beforeRegister()
	 * @return boolean whether the save should continue or not
	 */
	protected function beforeSave() {
		if ($this->scenario == "register" && !$this->beforeRegister()) {
			return false;
		}
		if ($this->isNewRecord || $this->password != $this->_password) {
			$this->salt = $this->generateSalt();
			$this->password = $this->hashPassword($this->password, $this->salt);
		}
		return parent::beforeSave();
	}
	/**
	 * Stores the hashed password so we can determine whether the password has been changed
	 * @see CActiveRecord::afterFind()
	 */
	protected function afterFind() {
		$this->_password = $this->password;
		parent::afterFind();
	}
	
	/**
	 * This method is invoked after a user is saved
	 * The default implementation raises the {@link onAfterRegister} and {@link onAfterSave} events.
	 * @see CActiveRecord::afterSave()
	 */
	protected function afterSave() {
		if ($this->scenario == "register") {
			$this->afterRegister();
		}
		parent::afterSave();
	}
	
	/**
	 * This method is invoked before a user registers with the site
	 * The default implementation raises the {@link onBeforeRegister} event.
	 * You may override this method to do any preparation work for user registration.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 * @return boolean whether the user should be allowed to register. Defaults to true.
	 */
	protected function beforeRegister() {
		if($this->hasEventHandler('onBeforeRegister'))
		{
			$event=new CModelEvent($this);
			$this->onBeforeRegister($event);
			return $event->isValid;
		}
		else
			return true;
	}
	
	/**
	 * This event is raised before a user registers.
	 * By setting {@link CModelEvent::isValid} to be false, the normal {@link save()} process will be stopped.
	 * @param CModelEvent $event the event parameter
	 */
	public function onBeforeRegister($event) {
		$this->raiseEvent('onBeforeRegister',$event);
	}
	
	/**
	 * This method is invoked after a user registers successfully
	 * The default implementation raises the {@link onAfterRegister} event.
	 * You may override this method to do postprocessing after registration.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 */
	protected function afterRegister() {
		Yii::log("[$this->id] User registered: $this->name ($this->email)","info","user.activity.register");
		if($this->hasEventHandler('onAfterRegister'))
			$this->onAfterRegister(new CEvent($this));
	}
	
	/**
	 * This event is raised after the user registers
	 * @param CEvent $event the event parameter
	 */
	public function onAfterRegister($event)	{
		$this->raiseEvent('onAfterRegister',$event);
	}
	
	/**
	 * Activates the user's account.
	 * @return boolean whether the account was activated or not
	 */
	public function activate() {
		if (!$this->beforeActivate()) {
			return false;
		}
		$this->isActive = true;
		if (!$this->save()) {
			return false;
		}
		$this->afterActivate();
	}
	
	/**
	 * This method is invoked before a user activates their account
	 * The default implementation raises the {@link onBeforeActivate} event.
	 * You may override this method to do any preparation work for account activation.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 * @return boolean whether the account can be activated. Defaults to true.
	 */
	protected function beforeActivate() {
		if($this->hasEventHandler('onBeforeActivate'))
		{
			$event=new CModelEvent($this);
			$this->onBeforeActivate($event);
			return $event->isValid;
		}
		else
			return true;
	}
	
	/**
	 * This event is raised before a user account is activated.
	 * By setting {@link CModelEvent::isValid} to be false, the normal {@link activate()} process will be stopped.
	 * @param CModelEvent $event the event parameter
	 */
	public function onBeforeActivate($event) {
		$this->raiseEvent('onBeforeActivate',$event);
	}
	
	/**
	 * This method is invoked after a user account is activated
	 * The default implementation raises the {@link onAfterActivate} event.
	 * You may override this method to do postprocessing after account activation.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 */
	protected function afterActivate() {
		Yii::log("[$this->id] User account activated: $this->name","info","user.activity.activate");
		if($this->hasEventHandler('onAfterActivate'))
			$this->onAfterActivate(new CEvent($this));
	}
	
	/**
	 * This event is raised after the user account is activated
	 * @param CEvent $event the event parameter
	 */
	public function onAfterActivate($event)	{
		$this->raiseEvent('onAfterActivate',$event);
	}
	
}
