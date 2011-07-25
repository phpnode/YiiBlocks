<?php
/**
 * An interface for objects that can be linked to. Objects that implement this
 * interface determine how they should be linked to.
 * @author Charles Pick
 * @package packages.linkable
 */
interface IALinkable {
	/**
	 * Creates an URL to an object
	 * @param string $action the action to link to, defaults to "view".
	 * @param array $parameters the URL parameters that should be included in the URL
	 * @param boolean $absolute whether to construct an absolute URL or not, defaults to false
	 * @return string the URL to this item
	 */
	public function createUrl($action = null, $params = null, $absolute = false);
	/**
	 * Creates a link to an object
	 * @see CHtml::link()
	 * @param string $anchorText the label to use for the link, this will not be URL encoded
	 * @param string $action the action to link to, defaults to "view".
	 * @param array $params the URL parameters that should be included in the URL
	 * @param array $htmlOptions the htmlOptions for the link
	 * @return string the link to this item
	 */
	public function createLink($anchorText = null, $params = null, $htmlOptions = array());
	
}
