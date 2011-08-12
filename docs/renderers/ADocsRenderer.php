<?php
/**
 * A base class for documentation renderers.
 * 
 * @author Charles Pick
 * @package packages.docs.renderers
 */
class ADocsRenderer extends CComponent {
	/**
	 * The docs generator
	 * @var ADocsGenerator
	 */
	public $generator;
	
	/**
	 * The name of the documentation theme to use
	 * @var string
	 */
	public $theme = "default";
	
	/**
	 * The documentation type
	 * @var string
	 */
	public $type = "html";
	
	/**
	 * The output directory for the documentation
	 * @var string
	 */
	public $outputDir;
	
	/**
	 * The title for the current documentation page
	 * @var string
	 */
	public $pageTitle;
	/**
	 * Renders the documentation
	 */
	public function render() {
		$this->renderClasses();
	}
	/**
	 * Renders the documetation for the classes
	 */
	public function renderClasses() {
		/*
		echo "<pre>";
				print_r($this->getAutocompleteData());
				return;*/
		$this->renderAssets();
		$autocomplete = "DocSearch.addData(".json_encode($this->getAutocompleteData()).");";
		file_put_contents($this->outputDir."/data.js",$autocomplete);
		file_put_contents($this->outputDir."/index.html",$this->renderLayout("index",array("namespace" => $this->generator->namespace)));
		file_put_contents($this->outputDir."/packages.html",$this->renderLayout("packages",array("namespace" => $this->generator->namespace)));
		foreach($this->generator->namespace->interfaces as $interface) {
			$filename = $this->outputDir."/".$interface->name."-interface.html";
			file_put_contents($filename,$this->renderLayout("interface",array("interface" => $interface)));
		}
		foreach($this->generator->namespace->classes as $class) {
			$filename = $this->outputDir."/".$class->name."-class.html";
			file_put_contents($filename,$this->renderLayout("class",array("class" => $class)));
		}
		foreach($this->generator->namespace->functions as $function) {
			$filename = $this->outputDir."/".$function->name."-function.html";
			file_put_contents($filename,$this->renderLayout("function",array("function" => $function)));
		}
	}
	/**
	 * Renders the assets - CSS + JavaScript for the documentation
	 */
	public function renderAssets() {
		CFileHelper::copyDirectory(Yii::getPathOfAlias("packages.docs.themes.".$this->theme.".".$this->type.".assets"),$this->outputDir."/assets/");
	}
	/**
	 * Renders a file and returns the content wrapped in a layout
	 * @param string $viewFile The view file to render
	 * @param array $data The data to pass to the view file
	 * @return string the rendered view file
	 */
	public function renderLayout($viewFile, $data = array()) {
		return $this->renderFile("layout",array("content" => $this->renderFile($viewFile,$data)));
	}
	/**
	 * Renders a file and returns the content
	 * @param string $viewFile The view file to render
	 * @param array $data The data to pass to the view file
	 * @return string the rendered view file
	 */
	public function renderFile($viewFile, $data = array()) {
		ob_start();
		extract($data);
		include(Yii::getPathOfAlias("packages.docs.themes.".$this->theme.".".$this->type)."/".$viewFile.".php");
		
		return ob_get_clean();
	}
	/**
	 * Renders the autocomplete data for the global namespace
	 * @return array the auto complete data
	 */
	public function getAutocompleteData() {
		$namespace = $this->generator->namespace;
		$data = array();
		foreach($namespace->interfaces as $interface) {
			$data[$interface->name."-interface"] = array(
				"label" => $interface->name,
				"value" => $interface->name."-interface.html",
				"introduction" => $interface->introduction,
				"type" => "interface",
			);
			foreach($interface->constants as $item) {
				$data[$interface->name."-interface-".$item->name."-constant"] = array(
					"label" => $interface->name.".".$item->name,
					"value" => $item->class->name."-interface.html#".$item->name."-constant",
					"type" => "constant",
					"introduction" => ($item->introduction !== null ? $item->introduction : ""), 
				);
			}
			foreach($interface->methods as $item) {
				$data[$interface->name."-interface-".$item->name."-method"] = array(
					"label" => $interface->name.".".$item->name."()",
					"value" => $item->class->name."-interface.html#".$item->name."-method",
					"type" => "method",
					"introduction" => ($item->introduction !== null ? $item->introduction : ""),
				);
			}
		} 
		foreach($namespace->classes as $class) {
			$data[$class->name."-class"] = array(
				"label" => $class->name,
				"value" => $class->name."-class.html",
				"type" => "class",
				"introduction" => ($class->introduction !== null ? $class->introduction : ""),
			);
			foreach($class->constants as $item) {
				$data[$class->name."-class-".$item->name."-constant"] = array(
					"label" => $class->name.".".$item->name,
					"value" => $item->class->name."-class.html#".$item->name."-constant",
					"type" => "constant",
					"introduction" => ($item->introduction !== null ? $item->introduction : ""),
				);
			}
			foreach($class->methods as $item) {
				$data[$class->name."-class-".$item->name."-method"] = array(
					"label" => $class->name.".".$item->name."()",
					"value" => $item->class->name."-class.html#".$item->name."-method",
					"type" => "method",
					"introduction" => ($item->introduction !== null ? $item->introduction : ""),
				);
			}
			foreach($class->properties as $item) {
				$data[$class->name."-class-".$item->name."-property"] = array(
					"label" => $class->name.".".$item->name,
					"value" => $item->class->name."-class.html#".$item->name."-property",
					"type" => "property",
					"introduction" => ($item->introduction !== null ? $item->introduction : ""),
				);
			}
		} 
		ksort($data);
		
		return array_values($data);
	}
	
	/**
	 * Generates a documentation link for an item of the specified type.
	 * If no such type can be found the type text will be returned instead 
	 * @param string $type the type, e.g. CComponent
	 * @return string the link to this type
	 */
	public function typeLink($type) {
		$link = $type;
		$fragment = null;
		if (strstr($type,"[")) {
			$type = array_shift(explode("[",$type));
		}
		if (strstr($type,"::")) {
			$type = explode("::",$type);
			$fragment = "#".array_pop($type);
			$type = array_shift($type);
		}
		elseif (strstr($type,".")) {
			$type = explode(".",$type);
			$fragment = "#".array_pop($type);
			$type = array_shift($type);
		}
		if ($fragment !== null) {
			if (substr($fragment,-2,2) == "()") {
				$fragment = substr($fragment,0,-2);
				$fragment .= "-method";
			}
			else {
				$fragment .= "-property";
			}
		}
		if (substr($type,-2,2) == "()") {
			$type = substr($type,0,-2);
		}
		if (isset($this->generator->namespace->classes[$type])) {
			$class = $this->generator->namespace->classes[$type];
			$link =  CHtml::link($class->name.substr($link,strlen($type)), $class->name."-class.html".$fragment);
		}
		return $link;
		
	}
	/**
	 * Highlights a PHP string
	 * @param string $code The PHP code to highlight
	 * @return string the highlighted code
	 */
	public function highlight($code) {
		$code=preg_replace("/^    /m",'',rtrim(str_replace("\t","    ",$code)));
		$code=highlight_string("<?php\n".$code,true);
		return preg_replace('/&lt;\\?php<br \\/>/','',$code,1);
	}
}
