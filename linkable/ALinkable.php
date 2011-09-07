<?php
/**
 * Adds createUrl() and createLink() methods to models to allow easy URL creation.
 * <b>Usage:</b>
 * <pre>
 * $user = User::model()->findByPK(123); // assumes user has the ALinkable behavior attached with the default settings
 * // url examples
 * echo $user->createUrl(); // outputs /user/123.html or similar depending on the url rules.
 * echo $user->createUrl("update"); // outputs /user/update/123.html or similar depending on the url rules.
 * echo $user->createUrl("update",array("someParam" => "someValue")); // outputs /user/update/123.html?someParam=someValue or similar depending on the url rules.
 *
 * // link examples
 * echo $user->createLink(); // outputs <a href='/user/123.html'>123</a> or similar depending on the url rules.
 * echo $user->createLink("Link Text",array("someParam" => "someValue"),array("class" => "blah")); // outputs <a href='/user/update/123.html?someParam=someValue' class='blah'>Link Text</a> or similar depending on the url rules.
 * </pre>
 * @author Charles Pick
 * @package packages.linkable
 */
class ALinkable extends CBehavior implements IALinkable {
	/**
	 * The route to the controller for this model.
	 * e.g. "/user"
	 * @var string
	 */
	public $controllerRoute;

	/**
	 * The name of the default action to use when creating URLs.
	 * Defaults to "view"
	 * @var string
	 */
	public $defaultAction = "view";

	/**
	 * The attributes that should be used when creating URLs.
	 * Defaults to null meaning use the owner's primary key
	 * @var array
	 */
	public $attributes;

	/**
	 * The template to use when creating links without any anchor text specified.
	 * e.g. if you wanted to use the attributes "name"
	 * and "id" when generating the anchor text:
	 * <pre>
	 * $this->template = "{name} ({id})";
	 * $user->createLink(); // outputs <a href='/user/123.html'>John Smith (123)</a> or similar
	 * </pre>
	 *
	 * Defaults to "{id}"
	 * @var string
	 */
	public $template = "{id}";

	/**
	 * Creates a URL for this model
	 * @param string $action The action to link to, if not specified the value of $this->defaultAction is used
	 * @param array $params The parameters to include in the URL, if null the value of $this->attributes will be used
	 * @param boolean $absolute Whether to create an absolute URL or not, defaults to false
	 * @return string The URL for this model
	 */
	public function createUrl($action = null, $params = null, $absolute = false) {
		if ($action === null) {
			$action = $this->defaultAction;
		}
		if ($params === null) {
			$params = array();
		}

		if (!is_array($params)) {
			$params = array();
		}
		else {
			if ($this->attributes === null) {
				if ($this->owner instanceof CActiveRecord) {
					$attributes = $this->owner->tableSchema->primaryKey;
				}
				elseif(isset($this->owner->id)) {
					$attributes = "id";
				}
				else {
					$attributes = array();
				}
			}
			else {
				$attributes = $this->attributes;
			}
			if (!is_array($attributes)) {
				$attributes = array($attributes);
			}
			foreach($attributes as $attribute) {
				if (!isset($params[$attribute]) || $params[$attribute] !== false) {
					$params[$attribute] = $this->owner->{$attribute};
				}
			}
		}
		$route = $this->controllerRoute;
		if ($route === null) {
			$route = "/".lcfirst(get_class($this->owner));
		}
		if (substr($route,-1,1) != "/") {
			$route .= "/";
		}
		$route .= $action;
		if (isset(Yii::app()->controller)) {
			if ($absolute) {
				return Yii::app()->controller->createAbsoluteUrl($route, $params);
			}
			else {
				return Yii::app()->controller->createUrl($route, $params);
			}
		}
		else {
			if ($absolute) {
				return Yii::app()->createAbsoluteUrl($route, $params);
			}
			else {
				return Yii::app()->createUrl($route, $params);
			}
		}
	}

	/**
	 * Creates a link to a model
	 * @param string $anchorText The anchor text to use for this link, defaults to null meaning use the value of $this->template as a template
	 * @param array $params A string refering to the action or an array of route parameters, see {@link CHtml::normalizeUrl}
	 * @param array $htmlOptions The htmlOptions, see {@link CHtml::link()}
	 * @return string The link to the model
	 */
	public function createLink($anchorText = null, $params = null, $htmlOptions = array()) {
		if (is_array($params) && isset($params[0])) {
			$action = $params[0];
			unset($params[0]);
		}
		elseif (is_string($params)) {
			$action = $params;
			$params = array();
		}
		else {
			$action = $this->defaultAction;
		}
		if ($anchorText === null) {
			$anchorText = $this->template;
			preg_match_all("/{(.*?)}/",$this->template,$matches);
			foreach($matches[1] as $n => $match) {
				if (strstr($match,":")) {
					$format = explode(":",$match);
					$attribute = array_shift($format);
					$format = array_shift($format);
					$anchorText = str_replace($matches[0][$n],Yii::app()->format->{$format}($this->owner->{$attribute}),$anchorText);
				}
				else {
					$anchorText = str_replace($matches[0][$n],CHtml::encode($this->owner->{$match}),$anchorText);
				}
			}

		}
		return CHtml::link($anchorText,$this->createUrl($action,$params),$htmlOptions);
	}

}
