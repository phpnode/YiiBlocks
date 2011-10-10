<?php
/**
 * Provides support for the QTip jQuery tooltip plugin.
 * @author Charles Pick
 * @package packages.qtip
 */
class AQtipWidget extends CWidget {
	/**
	 * The options to pass to the qtip plugin
	 * @var array
	 */
	public $options = array();

	/**
	 * The selector to bind this content to
	 * @var string
	 */
	public $selector;

	/**
	 * Initializes the widget and begins output buffering
	 */
	public function init() {
		parent::init();
		ob_start();
	}
	/**
	 * Runs the widget and registers the CSS and JavaScript
	 */
	public function run() {
		$content = trim(ob_get_clean());
		$baseUrl = $this->publishAssets();
		$cs = Yii::app()->clientScript;
		$cs->registerScriptFile($baseUrl."/jquery.qtip.js");
		$cs->registerCssFile($baseUrl."/jquery.qtip.css");
		if ($this->selector !== null) {
			$options = array();

			if ($content != "") {
				$options['content'] = $content;
			}
			$options = CJavaScript::encode(CMap::mergeArray($this->options,$options));
			$cs->registerScript(__CLASS__."#".$this->getId(),"jQuery('$this->selector').qtip($options);");
		}
	}
	/**
	 * Publishes the assets
	 * @return string the base URL for the assets
	 */
	public function publishAssets() {
		return Yii::app()->assetManager->publish(__DIR__."/assets/");
	}
}