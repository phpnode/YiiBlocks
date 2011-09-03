<?php
/**
 * Holds information about an email
 * @property integer $id the id of the email
 * @property string $sender the sender of the email
 * @property string $recipient the primary recipient of the email
 * @property string $cc the cc recipients
 * @property string $bcc the bcc recipients
 * @property string $subject the email subject
 * @property string $headers the email headers
 * @property string $content the email content
 * @property boolean $isHtml whether this is a html email or not
 * @property integer $timeAdded the time the email was added
 * 
 * @property AResource[] $attachments The email attachments, handled by the {@link AResourceful resourceful} behavior
 * 
 * @package packages.email.components
 * @author Charles Pick
 */
class AEmail extends CActiveRecord {
	/**
	 * The layout view to use when rendering this email 
	 * @var string
	 */
	public $layout;
	/**
	 * The view to use when rendering the content
	 * @var string
	 */
	public $view;
	/**
	 * The extra data to pass to the view
	 * @var array
	 */
	public $viewData = array();
	/**
	 * Holds the unique id for this email message
	 * @see getUniqueId()
	 * @var string
	 */
	protected $_uniqueId;
	/**
	 * Gets a unique id for this email message, used when encoding attachments.
	 * @return string the unique id
	 */
	public function getUniqueId() {
		if ($this->_uniqueId === null) {
			$this->_uniqueId = md5(uniqid());
		}
		return $this->_uniqueId;
	}
	/**
	 * Gets the behaviors to attach to this model
	 * @see CActiveRecord::behaviors()
	 * @return array the behaviors to attach to this model
	 */
	public function behaviors() {
		return array(
			"AResourceful" => array(
				"class" => "packages.resources.components.AResourceful",
				"attributes" => array(
					"attachments" => array(
						"multiple" => true,
					)
				)
			),
		);
	}
	/**
	 * Gets the name of the table to store emails in.
	 * @see CActiveRecord::tableName()
	 * @return string the table name
	 */
	public function tableName() {
		return "emails";
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return AEmail the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	/**
	 * Sends the email
	 * @param boolean $runValidation whether to run the validation or not, defaults to true
	 * @return boolean whether the email was sent successfully or not
	 */
	public function send($runValidation = true) {
		if ($runValidation && !$this->validate()) {
			return false;
		}
		return Yii::app()->getModule("email")->sender->send($this);
	}
	/**
	 * Renders the email in the layout and returns the contents.
	 * If there is no layout the email content will be returned
	 * @return string the rendered html
	 */
	public function render() {
		if ($this->layout === null && $this->view === null) {
			return $this->content;
		}
		if ($this->view === null) {
			return $this->renderPartial($this->layout,array("content" => $this->content));
		}
		elseif ($this->layout === null) {
			return $this->renderPartial($this->view,$this->viewData);
		}
		else {
			return $this->renderPartial($this->layout,
				array(
					"email" => $this,
					"content" => $this->renderPartial($this->view,$this->viewData)
					)
				);		
		}
	}
	/**
	 * Renders a view and returns the contents
	 * @param string $viewName the name of the view to render
	 * @param array $data the variables to make available to the view
	 * @return string the rendered view, or false if the  view cannot be found
	 */
	protected function renderPartial($viewName, $data = array()) {
		if(empty($viewName))
			return false;
		
		
		$extension='.php';
		$moduleViewPath=$basePath=Yii::app()->getViewPath();
		if (isset(Yii::app()->controller)) {
			if(($module=Yii::app()->controller->module)!==null) {
				$moduleViewPath=$module->getViewPath();
			}
		}
		$viewPath = Yii::app()->getModule("email")->getViewPath();
		if($viewName[0]==='/') {
			if(strncmp($viewName,'//',2)===0) {
				$viewFile = $basePath.$viewName;
			}
			else {
				$viewFile = $moduleViewPath.$viewName;
			}
		}
		else if(strpos($viewName,'.')) {
			$viewFile=Yii::getPathOfAlias($viewName);
		}
		else {
			$viewFile=$viewPath.DIRECTORY_SEPARATOR.$viewName;
		}
		
		if(is_file($viewFile.$extension)) {
			return $this->renderInternal($viewFile.$extension,$data);
		}
		else
			return false;
	}
	/**
	 * Renders the view and returns the contents
	 * @param string $_viewFile_ The view file to render
	 * @param array $_data_ The variables to make available in the view
	 * @return string The rendered view contents
	 */
	protected function renderInternal($_viewFile_,$_data_ = array()) {
		extract($_data_,EXTR_PREFIX_SAME,'data');
		ob_start();
		ob_implicit_flush(false);
		require($_viewFile_);
		return ob_get_clean();
	}
}
