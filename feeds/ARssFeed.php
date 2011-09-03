<?php
/**
 * Represents an RSS feed.
 * @author Charles Pick
 * @package packages.feeds
 */
class ARssFeed extends AFeed {
	/**
	 * The title to use for this feed
	 * @var string
	 */
	public $title;
	/**
	 * The description for this feed
	 * @var string
	 */
	public $description;
	/**
	 * The URL for this feed
	 * @var string
	 */
	public $url;
	/**
	 * The URL for the image to show for this feed, if any
	 * @var string
	 */
	public $imageUrl;
	/**
	 * The language for this feed, defaults to the application language
	 * @var string
	 */
	protected $_language;
	/**
	 * The language code for this RSS feed, defaults to the application language if not set
	 * @return string the language code
	 */
	public function getLanguage() {
		if ($this->_language === null) {
			$this->_language = Yii::app()->getLanguage();
		}
		return $this->_language;
	}
	/**
	 * Sets the language for this RSS feed
	 * @param string $value the language for this RSS feed
	 * @return string the language for this RSS feed
	 */
	public function setLanguage($value) {
		return $this->_language = $value;
	}
	/**
	 * Renders the RSS feed as an XML document
	 * @param bool $sendHeaders whether to send content type headers or not, defaults to true
	 * @param bool $return whether to return the content or not, defaults to false meaning echo the content
	 * @return string the XML for the RSS feed, if $return is true
	 */
	public function render($sendHeaders = true, $return = false) {
		$dom = new DOMDocument();
		$rss = $dom->createElement("rss");
		$rss->setAttribute("version", "2.0");
		$dom->appendChild($rss);
		$channel = $dom->createElement("channel");
		$rss->appendChild($channel);
		$channel->appendChild($dom->createElement("title",$this->title));
		$channel->appendChild($dom->createElement("description",$this->description));
		$channel->appendChild($dom->createElement("link",$this->url));
		$channel->appendChild($dom->createElement("language",$this->language));
		if ($this->imageUrl) {
			$image = $dom->createElement("image");
			$image->appendChild($dom->createElement("url",$this->imageUrl));
			$image->appendChild($dom->createElement("title",$this->title));
			$image->appendChild($dom->createElement("link",$this->url));
			$channel->appendChild($image);
		}
		foreach($this as /* @var AFeedItem $feedItem */ $feedItem) {
			$item = $dom->createElement("item");
			$item->appendChild($dom->createElement("title",$feedItem->title));
			$item->appendChild($dom->createElement("description",$feedItem->description));
			$item->appendChild($dom->createElement("pubDate",date('D, d M Y H:i:s T',$feedItem->timePublished)));
			$item->appendChild($dom->createElement("link",$feedItem->url));
			$item->appendChild($dom->createElement("guid",$feedItem->id));
			$channel->appendChild($item);
		}
		if ($sendHeaders) {
			header("Content-type: text/xml");
		}
		if ($return) {
			return $dom->saveXML();
		}
		else {
			echo $dom->saveXML();
		}
	}
}