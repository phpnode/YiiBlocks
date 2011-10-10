<?php
/**
 * Displays a calendar using the jQuery fullcalendar plugin
 * @author Charles Pick
 * @package packages.calendar.components
 */
class ACalendarWidget extends CWidget {
	/**
	 * The calendar model
	 * @var ACalendar
	 */
	public $model;
	/**
	 * The options to pass to the jQuery plugin
	 * @var array
	 */
	public $options = array();
	/**
	 * The htmlOptions for the container tag
	 * @var array
	 */
	public $htmlOptions = array();
	/**
	 * The name of the container tag.
	 * Defaults to "div"
	 * @var string
	 */
	public $tagName = "div";


	/**
	 * Displays the calendar
	 */
	public function run() {
		$baseUrl = $this->publishAssets();
		$cs = Yii::app()->clientScript;
		$cs->registerScriptFile($baseUrl."/fullcalendar.min.js");
		$cs->registerScriptFile($baseUrl."/ACalendarWidget.js");
		$cs->registerCssFile($baseUrl."/fullcalendar.css");
		$htmlOptions = $this->htmlOptions;
		$htmlOptions['id'] = $this->getId();
		$options = $this->options;
		if (!isset($options['events'])) {
			$options['events'] = $this->model->createUrl("eventData");
		}
		$options = CJavaScript::encode($options);
		$script = 'jQuery("#'.$htmlOptions['id'].'").fullCalendar('.$options.');';
		$script .= 'jQuery("#'.$htmlOptions['id'].'").ACalendarWidget();';

		$cs->registerScript(get_class($this)."#".$htmlOptions['id'],$script);
		echo CHtml::tag($this->tagName,$htmlOptions," ");
	}

	/**
	 * Publishes the JavaScript and CSS
	 * @return string the base URL for the assets
	 */
	public function publishAssets() {
		$assets = __DIR__.DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR.__CLASS__;
		return Yii::app()->assetManager->publish($assets);
	}
}