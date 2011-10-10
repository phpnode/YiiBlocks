<?php
Yii::import('zii.widgets.jui.CJuiInputWidget');
class AElrteWidget extends CJuiInputWidget {
	/**
	 * Runs the widget and displays the editor
	 */
	public function run() {
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
		$cs = Yii::app()->clientScript;
		$baseUrl = Yii::app()->assetManager->publish(dirname(__FILE__)."/assets/elrte");
		$cssFiles = array("/css/elrte.min.css");
		$jsFiles = array("/js/elrte.full.js");
		foreach($cssFiles as $cssFile) {
			$cs->registerCssFile($baseUrl.$cssFile);
		}

		foreach($jsFiles as $jsFile) {
			$cs->registerScriptfile($baseUrl.$jsFile);
		}
		$options = $this->options;
		$options = CJavaScript::encode($options);
		$script = <<<JS
	jQuery("#{$id}").elrte({$options});
JS;
		$cs->registerScript(__CLASS__."#".$id,$script);
		//echo CHtml::textArea($name, $value, $htmlOptions);
		echo CHtml::tag("div", $htmlOptions, $value);
	}
}
