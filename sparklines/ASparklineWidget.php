<?php
/**
 * Displays small charts using jQuery sparklines
 * @author Charles Pick
 * @package packages.sparklines
 */
class ASparklineWidget extends CWidget {
	/**
	 * The data to show in the chart
	 * @var array
	 */
	public $data;

	/**
	 * The htmlOptions for the container tag
	 * @var array
	 */
	public $htmlOptions = array();
	/**
	 * The options to pass to the jQuery sparkline plugin
	 * @var array
	 */
	public $options = array();
	/**
	 * The tag name for the container
	 * @var string
	 */
	public $tagName = "div";

	/**
	 * Displays the sparklines
	 */
	public function run() {
		$this->registerScripts();

		$id = $this->getId();
		$htmlOptions = $this->htmlOptions;
		$htmlOptions['id'] = $id;
		echo CHtml::tag($this->tagName,$htmlOptions," ");
		$data = CJavaScript::encode($this->data);
		$options = CJavaScript::encode($this->options);
		$script = "jQuery('#$id').sparkline($data, $options);";
		$cs->registerScript(__CLASS__."#".$id,$script);
	}
	/**
	 * Publishes the assets
	 * @return string the base url for the assets
	 */
	public function publishAssets() {
		return Yii::app()->assetManager->publish(__DIR__."/assets/");
	}

	/**
	 * Registers the scripts
	 */
	public function registerScripts() {
		$baseUrl = $this->publishAssets();
		$cs = Yii::app()->clientScript;
		$cs->registerScriptFile($baseUrl."/jquery.sparkline.js");
	}
}