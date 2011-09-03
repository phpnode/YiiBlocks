<?php
/**
 * ACKEditor providers a wrapper for the CKEditor wysiwyg html editor
 * @author Charles Pick
 * @package packages.ckeditor
 */
class ACKEditor extends CInputWidget {
	/**
	 * The options for the editor
	 * @var array
	 */
	public $options = array();
	/**
	 * Whether to use code mirror for the source view, defaults to true
	 * @var boolean
	 */
	public $useCodeMirror = true;
	/**
	 * The htmlOptions for the container
	 * @var array
	 */
	public $htmlOptions = array();

	private $_baseUrl;
	/**
	 * Runs the widget and shows the CKeditor
	 */
	public function run()
	{
		list($name, $id) = $this->resolveNameID();
		$htmlOptions = $this->htmlOptions;
		$htmlOptions['id'] = $id;
		$htmlOptions['name'] = $name;

		if ($this->hasModel()) {
			$value = $this->model->{$this->attribute};
		}
		else {
			$value = $this->value;
		}
		$this->registerAssets();
		$options = $this->options;
		if (!isset($options['skin']))	{
			$options['skin'] = "kama";
		}
		$options = CJavaScript::encode($options);
		$script = "var CKEDITOR_BASEPATH = '".$this->_baseUrl."/';\r\n";

		if (Yii::app()->request->isAjaxRequest)	{
			echo CHtml::tag("head",array(),CHtml::tag("script",array("type" => "text/javascript"),$script));
		}
		else {
			Yii::app()->clientScript->registerScript("ckeditor_basepath",$script);
		}

		echo CHtml::textArea($name, $value, $htmlOptions);

		Yii::app()->clientScript->registerScriptFile($this->_baseUrl.'/ckeditor.js');
		if ($this->useCodeMirror) {
			Yii::app()->clientScript->registerScriptFile($this->_baseUrl.'/plugins/codemirror/js/codemirror.js');
		}
		$script = "";
		$script = "CKEDITOR.replace('".$htmlOptions['id']."', ".$options.");\r\n";
		$script .= <<<EOD
		CKEDITOR.instances["{$htmlOptions['id']}"].on("instanceReady",
		function()
		{
			//set keyup event
			this.document.on("keyup", CK_jQ_{$htmlOptions['id']});

			 //and paste event
			this.document.on("paste", CK_jQ_{$htmlOptions['id']});
		});

		function CK_jQ_{$htmlOptions['id']}()
		{
		   CKEDITOR.instances.{$htmlOptions['id']}.updateElement();
		}
EOD;

		//echo CHtml::script($script);
		Yii::app()->clientScript->registerScript(__CLASS__."#".$id,$script);
		if ($this->useCodeMirror) {
			$script = <<<JS
	$("#{$htmlOptions['id']}").parents("form").first().bind("submit", function () {
		var i;
		for (i in CKEDITOR.instances) {
            CKEDITOR.instances[i].execCommand( 'mirrorSnapshot' );
        }
	});

JS;
			Yii::app()->clientScript->registerScript(__CLASS__."#".$id."#codeMirror",$script);
		}

	}
	/**
	 * Registers the assets used by ckeditor
	 */
	public function registerAssets()
	{
		$assets = dirname(__FILE__).DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR."ckeditor";
		$this->_baseUrl = Yii::app()->assetManager->publish($assets);

	}

}