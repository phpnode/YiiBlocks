<?php
/**
 * Displays the jQuery tag input plugin
 * @author Charles Pick
 * @package packages.tags.components
 */
class ATagInputWidget extends CInputWidget {
	/**
	 * The options to pass to the jQuery plugin
	 * @var array
	 */
	public $options = array();

	/**
	 * Displays the tag input widget
	 */
	public function run() {
		list($name, $id) = $this->resolveNameID();
		$htmlOptions = $this->htmlOptions;
		$htmlOptions["id"] = $id;
		if ($this->hasModel()) {
			$value = $this->model->{$this->attribute};
		}
		else {
			$value = $this->value;
		}
		if (is_array($value)) {
			$v = array();
			foreach($value as $item) {
				$v[] = $item->tag;
			}
			$value = implode(",",$v);
		}
		echo CHtml::textField($name,$value,$htmlOptions);
		$baseUrl = $this->publishAssets();
		$options = $this->options;
		if (isset($options['autocomplete_url']) && is_array($options['autocomplete_url'])) {
			$options['autocomplete_url'] = $this->controller->createUrl(array_shift($options['autocomplete_url']),$options['autocomplete_url']);
		}
		$options = CJavaScript::encode($options);
		$script = "$('#$id').tagsInput($options)";
		Yii::app()->clientScript->registerScriptFile($baseUrl."/jquery.tagsinput.min.js");
		Yii::app()->clientScript->registerCssFile($baseUrl."/jquery.tagsinput.css");
		Yii::app()->clientScript->registerScript(__CLASS__.":".$id,$script);
	}
	/**
	 * Publishes the assets
	 * @return string the assets baseUrl
	 */
	protected function publishAssets() {
		$assets = __DIR__."/assets/".__CLASS__."/";
		return Yii::app()->assetManager->publish($assets);
	}
}