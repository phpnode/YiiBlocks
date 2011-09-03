<?php
/**
 * Displays moderation buttons for moderatable items
 * @author Charles Pick
 * @package packages.moderator.widgets
 */
class AModerationButtons extends CWidget {
	/**
	 * Holds the model being moderated.
	 * @var CActiveRecord
	 */
	public $model;
	/**
	 * Whether to show the buttons, defaults to true.
	 * Set this to false if you want to display the buttons yourself
	 * @var boolean
	 */
	public $showButtons = true;
	
	/**
	 * The htmlOptions for the container div
	 * @var array
	 */
	public $htmlOptions = array();
	
	/**
	 * The options to be passed to the jQuery plugin
	 * @var array
	 */
	public $options = array();
	
	/**
	 * CSS class for the approve button.
	 * @var array
	 */
	public $approveClass = "approve button";
	/**
	 * CSS class for the deny button.
	 * @var array
	 */
	public $denyClass = "deny button";
	
	
	/**
	 * Displays the moderation buttons and registers
	 * the required JavaScript.
	 */
	public function run() {
		$id = $this->getId();
		$htmlOptions = $this->htmlOptions;
		$htmlOptions['id'] = $id;
	
		if ($this->showButtons) {
			echo CHtml::openTag("div",$htmlOptions);
			$moderationItem = $this->model->moderationItem;
			if ($moderationItem === false) {
				$moderationItem = new AModerationItem;
				$moderationItem->ownerModel = $this->model->getClassName();
				$moderationItem->ownerId = $this->model->getId();
			}
			echo $moderationItem->createLink($moderationItem->status == "approved" ? "Approved" : "Approve",array("approve"),array("class" => $this->approveClass));
			echo $moderationItem->createLink($moderationItem->status == "denied" ? "Denied" : "Deny",array("deny"),array("class" => $this->denyClass));
			echo CHtml::closeTag("div");
		}
		
		$this->registerScripts();
		$options = array(
				"selectors" => array(
						"approve" => "a.".implode(".",explode(" ",$this->approveClass)),
						"deny" => "a.".implode(".",explode(" ",$this->denyClass)),
					),
				"postData" => array()
			);
		if (Yii::app()->request->enableCsrfValidation) {
			$options['postData'][Yii::app()->request->csrfTokenName] = Yii::app()->request->csrfToken;
		}
		$options = CMap::mergeArray($options,$this->options);
		if (function_exists("json_encode")) {
			$options = json_encode($options);
		}
		else {
			$options = CJSON::encode($options);
		} 
		$script = "$('#$id').AModerationButtons($options);";
		Yii::app()->clientScript->registerScript(__CLASS__."#".$id,$script);
	}
	/**
	 * Registers the required scripts
	 */
	public function registerScripts() {
		$baseUrl = Yii::app()->assetManager->publish(dirname(__FILE__)."/assets/".__CLASS__);
		Yii::app()->clientScript->registerScriptFile($baseUrl."/AModerationButtons.js");
		
	}
}
