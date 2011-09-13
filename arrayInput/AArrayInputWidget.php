<?php
/**
 * Provides a basic interface for editing arrays
 * @author Charles Pick
 * @package application.modules.admin.components
 */
class AArrayInputWidget extends CInputWidget {

	/**
	 * The htmlOptions for the input widget wrapper
	 * @var array
	 */
	public $htmlOptions = array();

	/**
	 * The class for the input widget wrapper, defaults to "arrayInput"
	 * @var string
	 */
	public $containerClass = "arrayInput";

	/**
	 * The name of the html element to wrap the input widget in
	 * defaults to "div"
	 * @var string
	 */
	public $tagName = "div";

	/**
	 * The css class name for the tables that contain the input fields
	 * defaults to "array"
	 * @var string
	 */
	public $tableClass = "array";

	/**
	 * The css class name for item keys in the array, defaults to "array-key"
	 * @var string
	 */
	public $keyClass = "array-key";

	/**
	 * The css class name for item values in the array, defaults to "array-value"
	 * @var string
	 */
	public $valueClass = "array-value";

	/**
	 * The css class name for multidimensional arrays, defaults to "array-multi"
	 * @var string
	 */
	public $multiClass = "array-multi";

	/**
	 * Runs the widget and displays the input field
	 */
	public function run() {
		list($name,$id) = $this->resolveNameID();
		$htmlOptions = $this->htmlOptions;
		$htmlOptions['id'] = $id;
		if (!isset($htmlOptions['class'])) {
			$htmlOptions['class'] = $this->containerClass;
		}
		if ($this->hasModel()) {
			$value = $this->model->{$this->attribute};
		}
		else {
			$value = $this->value;
		}
		if ($value instanceof Traversable) {
			$value = (array) $value;
		}
		if (!is_array($value)) {
			$value = array();
		}
		echo CHtml::openTag($this->tagName, $htmlOptions);
		echo CHtml::hiddenField($name, "");
		echo $this->renderArray($value, $name);
		echo CHtml::closeTag($this->tagName);
		$baseUrl = Yii::app()->assetManager->publish(__DIR__."/assets");
		Yii::app()->clientScript->registerScriptFile($baseUrl."/AArrayInputWidget.js");
		$script = "jQuery('#$id').arrayInputWidget()";
		Yii::app()->clientScript->registerScript(get_class($this)."#".$id,$script);
	}

	/**
	 * Returns the html containing the input fields for this array
	 * @param array $array The input array
	 * @param string $prefix The prefix for the input field names
	 * @return string The html containing the input fields for this array
	 */
	public function renderArray($array, $prefix) {
		$html = "";
		$html .= "<table class='".$this->tableClass."' rel='".$prefix."'>\n";
		$html .= "<thead>\n";
		$html .= "<th>Key</th>\n";
		$html .= "<th>Value</th>\n";
		$html .= "<th class='actions'><a href='#' class='icon add iconOnly' title='Add'>&nbsp;</a></th>\n";
		$html .= "<th class='actions'><a href='#' class='icon flattenArray iconOnly' title='Flatten this array'>&nbsp;</a></th>\n";
		$html .= "</thead>\n";
		$html .= "<tbody>\n";

		foreach($array as $key => $value) {
			$html .= "<tr>\n";
			$html .= "<th class='".$this->keyClass.(is_array($value) ? " ".$this->multiClass : "")."'>\n";
			$html .= CHtml::textField(null, $key);
			$html .= "</th>\n";


			if (is_array($value)) {

				$html .= "<td colspan='2'>\n";
				$html .= $this->renderArray($value, $prefix."[".$key."]");
			}
			else {
				$html .= "<td class='".$this->valueClass."'>\n";

				$html .= CHtml::textField($prefix."[".$key."]",$value);
			}
			$html .= "</td>\n";

			$html .= "<th class='actions'><a href='#' class='icon delete iconOnly' title='Delete'>&nbsp;</a></th>\n";
			if (!is_array($value)) {
				$html .= "<th class='actions'><a href='#' class='icon convertArray iconOnly' title='Convert to array'>&nbsp;</a></th>\n";
			}

			$html .= "</tr>\n";
		}
		$html .= "<tr class='empty'>\n";
		$html .= "<td colspan='4' class='".(count($array) ? "hidden" : "")." empty'>Empty array</td>\n";
		$html .= "</tr>\n";

		$html .= "</tbody>\n";
		$html .= "</table>\n";
		return $html;
	}
}
