<?php
Yii::import("zii.widgets.CPortlet");
/**
 * A base class for admin portlets
 * @author Charles Pick
 * @package packages.admin.components
 */
class AAdminPortlet extends CPortlet {
	/**
	 * The tag name for the container
	 * @var string
	 */
	public $tagName = "article";
	/**
	 * @var array the HTML attributes for the portlet container tag.
	 */
	public $htmlOptions=array('class'=>'adminPortlet grid_12 alpha omega');

	/**
	 * @var string the CSS class for the decoration container tag.
	 */
	public $decorationCssClass='';
	/**
	 * @var string the CSS class for the portlet title tag.
	 */
	public $titleCssClass='';
	/**
	 * @var string the CSS class for the content container tag. Defaults to 'content'.
	 */
	public $contentCssClass='content';
	/**
	 * An array of CMenu items to show in the header.
	 * If this array is not set no menu will be shown
	 * @var array
	 */
	public $menuItems;
	/**
	 * The configuration for the header menu, if shown
	 * @var array
	 */
	public $menuConfig = array("htmlOptions" => array("class" => "menu"));
	/**
	 * An array of CMenu items to show in the sidebar.
	 * If this array is not set no sidebar will be shown
	 * @var array
	 */
	public $sidebarMenuItems;
	/**
	 * The configuration for the sidebar menu, if shown
	 * @var array
	 */
	public $sidebarMenuConfig = array("htmlOptions" => array("class" => "menu"));
	/**
	 * The htmlOptions for the sidebar, if shown
	 * @var array
	 */
	public $sidebarHtmlOptions = array("class" => "sidebar");
	/**
	 * Extra content to show in the sidebar, this will not be html encoded!
	 * @var string
	 */
	public $sidebarContent;

	private $_openTag;
	/**
	 * Initializes the widget.
	 * This renders the open tags needed by the portlet.
	 * It also renders the decoration, if any.
	 */
	public function init() {
		ob_start();
		ob_implicit_flush(false);

		$this->htmlOptions['id']=$this->getId();
		echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
		$this->renderDecoration();
		$this->renderSidebar();
		echo "<section class=\"{$this->contentCssClass}\">\n";

		$this->_openTag=ob_get_contents();
		ob_clean();
	}

	/**
	 * Renders the content of the portlet.
	 */
	public function run() {
		$this->renderContent();
		$content=ob_get_clean();
		if($this->hideOnEmpty && trim($content)==='')
			return;
		echo $this->_openTag;
		echo $content;
		echo "</section>\n";
		echo CHtml::closeTag($this->tagName);
	}
	/**
	 * Shows a sidebar with menu and extra content if possible
	 */
	protected function renderSidebar() {
		if ($this->sidebarContent == "" && (!is_array($this->sidebarMenuItems) || count($this->sidebarMenuItems) == 0)) {
			return;
		}
		echo CHtml::openTag("section",$this->sidebarHtmlOptions);
		if (is_array($this->sidebarMenuItems) && count($this->sidebarMenuItems)) {
			$menuConfig = $this->sidebarMenuConfig;
			$menuConfig['items'] = $this->sidebarMenuItems;
			$this->widget("zii.widgets.CMenu",$menuConfig);
		}
		echo "</section>\n";
	}

	/**
	 * Renders the decoration for the portlet.
	 * The default implementation will render the title if it is set.
	 */
	protected function renderDecoration()
	{
		if($this->title!==null) {
			echo "<header class=\"{$this->decorationCssClass}\">\n";
			echo "<h1 class=\"{$this->titleCssClass}\">{$this->title}</h1>\n";
			if (is_array($this->menuItems) && count($this->menuItems)) {
				$menuConfig = $this->menuConfig;
				$menuConfig['items'] = $this->menuItems;
				$this->widget("zii.widgets.CMenu",$menuConfig);
			}
			echo "</header>\n";
		}
	}
}