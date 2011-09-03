<?php
Yii::import("packages.users.portlets.AUserProfilePortlet");
/**
 * Shows a list of details for the user
 * @author Charles Pick
 * @package packages.users.portlets
 */
class AUserDetailsPortlet extends AUserProfilePortlet {
	/**
	 * The user model attributes to show.
	 * An attribute can be specified as a string in the format of "name:type:label", both type and label are optional.
	 * An attribute can also be specified as an array, with the following format:
	 * <li>label - The label to show for the attribute</li>
	 * <li>name - The name of the attribute</li>
	 * <li>type - The type of the attribute which determines how it wil be formatted</li>
	 * <li>value - The value to show, this will be formatted according to the type attribute</li>
	 * @var array
	 */
	public $attributes;
	/**
	 * The template to use 
	 */
	public $itemTemplate = "{label} {value}\n";
	
	/**
	 * The tag name for the item
	 * @var string
	 */
	public $itemTagName = "div";
	/**
	 * The CSS class to use for items.
	 * Defaults to "item"
	 * @var string
	 */
	public $itemCssClass = "item";
	/**
	 * The label tag name.
	 * Defaults to "span".
	 * @var string
	 */
	public $labelTagName = "span";
	
	/**
	 * The css class for the label
	 * Defaults to "label"
	 * @var string
	 */
	public $labelCssClass = "label";
	
	/**
	 * The item value tag name.
	 * Defaults to "span".
	 * @var string
	 */
	public $valueTagName = "span";
	
	/**
	 * The css class for the item value
	 * Defaults to "value"
	 * @var string
	 */
	public $valueCssClass = "value";
	
	/**
	 * Holds the CFormatter instance
	 * @var CFormatter
	 */
	protected $_formatter;
	
	/**
	 * Renders the details for the user
	 * @see CPortlet::renderContent()
	 */
	protected function renderContent() {
		$attributes = $this->normaizeAttributes();
		foreach($attributes as $attribute) {
			$item = array(
				"{label}" => CHtml::tag($this->labelTagName,array("class" => $this->labelCssClass),$attribute['label']),
				"{value}" => CHtml::tag($this->valueTagName,array("class" => $this->valueCssClass),$attribute['value']),
			);
			echo CHtml::tag($this->itemTagName,array("class" => $this->itemCssClass),strtr($this->itemTemplate,$item));
		}
	}
	/**
	 * Normalizes the attribute list
	 * @return array The normalized attribute configuration
	 */
	protected function normaizeAttributes() {
		if (empty($this->attributes)) {
			$attributes = array();
			foreach($this->user->attributes as $attribute => $value) {
				if (stristr($attribute,"password") || stristr($attribute,"salt")) {
					continue;
				}
				$attributes[] = array(
					"name" => $attribute,
					"label" => $this->user->getAttributeLabel($attribute),
					"value" => $value,
					"type" => "text"
				);
			}
		}
		else {
			$attributes = $this->attributes;
		}
		foreach($attributes as $i => $attribute) {
			if (is_string($attribute)) {
				if (strstr($attribute,":")) {
					$a = explode(":",$attribute);
					$attribute['name'] = array_shift($a);
					$attribute['type'] = array_shift($a);
					$attribute['label'] = array_shift($a);
				}
				else {
					$attribute = array("name" => $attribute);
				}
			}
			if (!isset($attribute['type']) || empty($attribute['type'])) {
				$attribute['type'] = "text";
			}
			if (!isset($attribute['label']) || empty($attribute['label'])) {
				$attribute['label'] = $this->user->getAttributeLabel($attribute['name']);
			}
			if (!isset($attribute['value']) || empty($attribute['value'])) {
				$attribute['value'] = $this->user->{$attribute['name']};
			}
			$attribute['value'] = $this->formatter->format($attribute['value'],$attribute['type']);
			$attributes[$i] = $attribute;
		}


		return $attributes;
	}
	/**
	 * Gets the formatter instance to use when formatting item values
	 * @return CFormatter the formatter
	 */
	public function getFormatter() {
		if ($this->_formatter === null) {
			return Yii::app()->format;
		}
		return $this->_formatter;
	}
	/**
	 * Sets the formatter to use when formatting item values
	 * @param CFormatter $formatter The formatter instance to use
	 * @return CFormatter the formatter to use
	 */
	public function setFormatter(CFormatter $formatter) {
		return $this->_formatter = $formatter;
	}
}
