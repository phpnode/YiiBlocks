<?php
Yii::import("packages.users.portlets.AUserProfilePortlet");
/**
 * Shows 
 * @author Charles Pick
 * @package packages.users.portlets
 */
class AUserImagePortlet extends AUserProfilePortlet {
	/**
	 * The name of the attribute that points to the image
	 * @var string
	 */
	public $attribute;
	/**
	 * Holds the htmlOptions for the image
	 * @var array
	 */
	public $imageOptions = array();
	/**
	 * Holds the image alt text
	 * @var string
	 */
	protected $_altText;
	/**
	 * Holds the image URL
	 * @var string
	 */
	protected $_imageUrl;
	/**
	 * Holds the default image URL, this will be shown if no image is available
	 * @var string
	 */
	protected $_defaultImageUrl;
	/**
	 * Renders the image for the user
	 * @see CPortlet::renderContent()
	 */
	protected function renderContent() {
		echo CHtml::image($this->imageUrl,$this->altText,$this->imageOptions);
	}
	/**
	 * Gets the URL of the image to display
	 * @return string the image url
	 */
	public function getImageUrl() {
		if ($this->_imageUrl !== null) {
			return $this->_imageUrl;
		}
		if ($this->attribute !== null && isset($this->user->{$this->attribute})) {
			$url = $this->user->{$this->attribute};
			if (is_string($url)) {
				return $url;
			}
			elseif ($url instanceof AResource) {
				return $url->url;
			}
		}
		
		return $this->defaultImageUrl;
	}
	/**
	 * Sets the URL for the image to display
	 * @param string $url the url for the image
	 * @return string the url for the image
	 */
	public function setImageUrl($url) {
		return $this->_imageUrl = $url;
	}
	
	/**
	 * Gets the default image URL
	 * @return string the url for the default image
	 */
	public function getDefaultImageUrl() {
		if ($this->_defaultImageUrl === null) {
			$baseUrl = Yii::app()->assetManager->publish(dirname(__FILE__)."/assets/".__CLASS__."/");
			$this->_defaultImageUrl = $baseUrl."/no-profile-image.jpg"; 
		}
		return $this->_defaultImageUrl;
	}
	
	/**
	 * Sets the URL for the default image to display when an image cannot be found
	 * @param string $url the url for the default image
	 * @return string the url for the default image
	 */
	public function setDefaultImageUrl($url) {
		return $this->_defaultImageUrl = $url;
	}
	/**
	 * Gets the alt text for the image
	 * @return string the alt text for the image
	 */
	public function getAltText() {
		if ($this->_altText === null) {
			$this->_altText = $this->user->name;
		}
		return $this->_altText;
	}
	/**
	 * Sets the alt text for this image
	 * @param string $text the alt text
	 * @return string the alt text
	 */
	public function setAltText($text) {
		return $this->_altText = $text;
	}
}
