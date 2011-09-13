<?php
/**
 * Displays charts using jqPlot
 * @author Charles Pick
 * @package packages.plotcharts
 */
class APlotChartWidget extends CWidget {
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
	 * The options to pass to the jQuery plot plugin
	 * @var array
	 */
	public $options = array();
	/**
	 * The tag name for the container
	 * @var string
	 */
	public $tagName = "div";

	/**
	 * The css file to use for jQuery plot charts.
	 * Defaults to null meaning use the default CSS file.
	 * To use a custom style sheet, set this to a custom URL.
	 * Set this to false to skip registering the CSS file.
	 * @var mixed
	 */
	public $cssFile;

	/**
	 * An array of jqPlot plugin names to register
	 * @var array
	 */
	public $plugins = array();
	/**
	 * Displays the chart
	 */
	public function run() {
		$baseUrl = $this->publishAssets();
		$cs = Yii::app()->clientScript;
		$cs->registerScriptFile($baseUrl."/jquery.jqplot.js");
		foreach($this->plugins as $plugin) {
			$cs->registerScriptFile($baseUrl."/plugins/jqplot.".$plugin.".min.js");
		}
		if ($this->cssFile === null) {
			$cs->registerCSSFile($baseUrl."/jquery.jqplot.css");
		}
		else if ($this->cssFile) {
			$cs->registerCssFile($this->cssFile);
		}
		$id = $this->getId();
		$htmlOptions = $this->htmlOptions;
		$htmlOptions['id'] = $id;
		echo CHtml::tag($this->tagName,$htmlOptions," ");
		$data = CJavaScript::encode($this->data);
		$options = CJavaScript::encode($this->options);
		$script = "jQuery.jqplot('$id', $data, $options);";
		$cs->registerScript(__CLASS__."#".$id,$script);
	}
	/**
	 * Publishes the assets
	 * @return string the base url for the assets
	 */
	public function publishAssets() {
		return Yii::app()->assetManager->publish(__DIR__."/assets/");
	}
}