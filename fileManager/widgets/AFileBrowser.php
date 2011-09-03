<?php
/**
 * A simple file browser widget
 * @author Charles Pick
 * @package packages.fileBrowser
 */
class AFileBrowser extends CInputWidget {
	/**
	 * The base path for this file browser. 
	 * Files and folders under this directory will be
	 * visible to the client unless they're specifically
	 * excluded by $this->excludeFiles.
	 * This can either be a string or an array of values
	 * @var mixed
	 */
	public $basePath;
	/**
	 * The htmlOptions for the directory browser
	 * tree view
	 * 
	 */
	public $directoryBrowserOptions = array();
	
	/**
	 * An array of files / directories to exclude.
	 * @var array
	 */
	public $exclude = array(".git",".svn",".cvs");
	
	/**
	 * Whether to allow multiple selections or not.
	 * Defaults to false.
	 * @var boolean
	 */
	public $multiple = false;
	
	/**
	 * The options to pass to the jQuery plugin.
	 * @var array
	 */
	public $options = array();
	
	/**
	 * Holds a list of directories
	 * @var array
	 */
	protected $_directories;
	
	/**
	 * Displays the widget
	 */
	public function run() {
		if ($this->hasModel()) {
			$value = $this->model->{$this->attribute};
		}
		else {
			$value = $this->value;
		}
		list($name, $id) = $this->resolveNameID();
		echo CHtml::tag("div",array("id" => $id),$this->renderDirectories().$this->renderFiles());
		
		$options = array(
			"multiple" => $this->multiple
		);
		$options = CMap::mergeArray($options,$this->options);
		if (function_exists("json_encode")) {
			$options = json_encode($options);
		}
		else {
			$options = CJSON::encode($options);
		} 
		$script = "$('#$id').AFileBrowser($options);";
		$this->registerScripts();
		Yii::app()->clientScript->registerScript(__CLASS__."#".$id,$script);
	}
	
	/**
	 * Renders the directory browser treeview
	 * @return string The HTML for the directory browser
	 */
	public function renderDirectories() {
		$treeData = array();
		list($name, $id) = $this->resolveNameID();
		$htmlOptions = array(
			"class" => "filetree",
			"id" => $id."-directories"
		);
		
		foreach((is_array($this->basePath) ? $this->basePath : array($this->basePath)) as $path) {
			$item = array(
				"text" => "<span class='folder'>".basename($path)."</span>",
				"id" => md5($path),
				"children" => $this->directoryTreeviewData($path),
				"expanded" => false,
			);
			$checkboxId = $this->getId()."_".$item['id'];
			$item['text'] = CHtml::tag(
						"span",
						array(
							"class" => "folder"
						),
						($this->multiple ? 
							CHtml::activeCheckbox($this->model,$this->attribute."[".$item['id']."]",array("id" => $checkboxId))
							:
							CHtml::activeRadioButton($this->model,$this->attribute,array("id" => $checkboxId, "value" => $item['id']))
						).
						CHtml::label(basename($path),$checkboxId)
					);
			$treeData[] = $item;
			
		}
		ob_start();
		$this->widget("CTreeView",array(
			"htmlOptions" => $htmlOptions,
			"data" => $treeData
		));
		$html = ob_get_clean();
		return $html;
	}
	/**
	 * Renders the file browser
	 * @return string The html for the file browser
	 */
	public function renderFiles() {
		list($name, $id) = $this->resolveNameID();
		$id .= "-files";
		$basePath = $this->basePath;
		if (is_array($basePath)) {
			$basePath = array_shift($basePath); // the first folder is selected by default
		}
		ob_start();
		$this->widget('zii.widgets.jui.CJuiSelectable', array(
			'items'=>array(
				'id1'=>'<strong>blah</strong> Item 1',
				'id2'=>'Item 2',
				'id3'=>'Item 3',
			),
			'id' => $id,
		));
		Yii::app()->clientScript->registerCSS(__CLASS__.":".$id,<<<CSS
#$id .ui-selecting { background: #FECA40; }
#$id .ui-selected { background: #F39814; color: white; }
#$id { list-style-type: none; margin: 0; padding: 0; }
#$id li {
	margin: 3px;
	padding: 10px;
	float: left;
	width: 60px;
	height: 40px;
	text-align: center;
	}
		
CSS
		);
		$html = ob_get_clean();
		return $html;
	}
	/**
	 * Gets the directory tree view data for the specified base path
	 * @param string $basePath the path to search for directories under
	 * @return array An array of tree view items
	 */
	public function directoryTreeviewData($basePath) {
		$items = array();
		list($name, $id) = $this->resolveNameID();
		foreach(AFileHelper::findDirectories($basePath,array(
			"level" => 0,
			"exclude" => $this->exclude,
		)) as $dir) {
			$item = array();
			$item['id'] = md5($dir);
			$checkboxId = $id."_".$item['id'];
			if ($this->hasModel()) {
				$item['text'] = CHtml::tag(
						"span",
						array(
							"class" => "folder"
						),
						($this->multiple ? 
							CHtml::activeCheckbox($this->model,$this->attribute."[".$item['id']."]",array("id" => $checkboxId))
							:
							CHtml::activeRadioButton($this->model,$this->attribute,array("id" => $checkboxId, "value" => $item['id']))
						).
						CHtml::label(basename($dir),$checkboxId)
					);
			}
			
			$item['children'] = $this->directoryTreeViewData($dir);
			$item['expanded'] = false;
			$items[] = $item;
		}
		return $items;
	}
	
	/**
	 * Gets a list of directories under the basePath
	 * @return array An array of directories under the basePath
	 */
	public function getDirectories() {
		if ($this->_directories === null) {
			$this->_directories = array();
			$basePath = $this->basePath;
			if (!is_array($basePath)) {
				$basePath = array($basePath);
			}
			foreach($basePath as $path) {
				$this->_directories[$path] = array();
				foreach(AFileHelper::findDirectories($path) as $dir) {
					$this->_directories[$path][md5($dir)] = $dir; 
				}
			}
		}
		return $this->_directories;
	}
	
	/**
	 * Registers the required scripts
	 */
	public function registerScripts() {
		$baseUrl = Yii::app()->assetManager->publish(dirname(__FILE__)."/assets/".__CLASS__);
		Yii::app()->clientScript->registerScriptFile($baseUrl."/AFileBrowser.js");
		
	}
}
