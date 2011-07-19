<?php
/**
 * Displays a view file for a model, useful when you regularly want to display
 * information from a model in a certain way, e.g. a search result.
 * <pre>
 * $model = new User;
 * $model->attachBehavior("decoration",new AModelDecorator());
 * echo $model->decorate("searchResult"); // returns the contents of the view file called searchResult.php under protected/views/user/
 * echo $model->decorate("application.views.user.searchResult"); // aliases are supported too
 * // changing the path to the views:
 * $model->decoratorPath = "application.views.somethingElse";
 * echo $model->decorate("searchResult"); // now looks for searchResult.php under protected/views/somethingElse
 * </pre>
 * 
 * The decorator can also be used as a widget, e.g.
 * <pre>
 * $this->beginWidget("blocks.decorating.AModelDecorator",array(
 * 	"model" => $model,
 *  "viewName" => "searchResult"
 * ));
 * </pre>
 */
class AModelDecorator extends CWidget implements IBehavior {
	/**
	 * Holds the model being decorated
	 * @var CModel
	 */
	public $model;
	
	/**
	 * The name of the view that should be rendered.
	 * Defaults to "_view"
	 * @var string
	 */
	public $viewName = "_view";
	
	/**
	 * Extra data that should be passed to the view along with the model.
	 * @var array
	 */
	public $extraData = array();
	
	/**
	 * Holds the path to the decorator views for this model.
	 * @var string
	 * @see getDecoratorPath()
	 * @see setDecoratorPath()
	 */
	protected $_decoratorPath;
	
	
	
	private $_enabled;


	/**
	 * Attaches the behavior object to the component.
	 * The default implementation will set the {@link owner} property.
	 * Make sure you call the parent implementation if you override this method.
	 * @param CModel $owner the component that this behavior is to be attached to.
	 */
	public function attach($owner) {
		$this->model=$owner;
	}

	/**
	 * Detaches the behavior object from the component.
	 * The default implementation will unset the {@link owner} property.
	 * Make sure you call the parent implementation if you override this method.
	 * @param CComponent $owner the component that this behavior is to be detached from.
	 */
	public function detach($owner)	{
		$this->model=null;
	}


	/**
	 * @return boolean whether this decorator is enabled
	 */
	public function getEnabled()
	{
		return $this->_enabled;
	}

	/**
	 * @param boolean $value whether this decorator is enabled
	 */
	public function setEnabled($value) {
		$this->_enabled=$value;
	}
	
	/**
	 * Gets the decorator view path for this model.
	 * If this is null the path to the model will be used as a base for
	 * constructing the decorator path. E.g. a model called protected/models/User.php
	 * has a default decorator path of protected/views/user/. A model called protected/modules/mymodule/models/MyModel.php
	 * has a default decorator path of protected/modules/mymodule/views/mymodel/
	 * @return string
	 */
	public function getDecoratorPath() {
		if ($this->_decoratorPath === null) {
			$reflection = new ReflectionClass($this->model);
			$this->_decoratorPath = substr(dirname(dirname($reflection->getFileName())),strlen(Yii::app()->basePath));
			$this->_decoratorPath = "application".str_replace(DIRECTORY_SEPARATOR,".",$this->_decoratorPath).".views.".lcfirst($reflection->getName());
		}
		return $this->_decoratorPath;
	}
	/**
	 * Sets the decorator view path for this model.
	 * @see getDecoratorPath()
	 * @param string $path The alias of the view folder, if a path is specified it will be converted into an alias
	 */
	public function setDecoratorPath($path) {
		if (strstr($path,DIRECTORY_SEPARATOR)) {
			if (substr($path,0,strlen(Yii::app()->basePath)) === Yii::app()->basePath) {
				$path = substr($path,strlen(Yii::app()->basePath));
			}
			$path = "application".str_replace(DIRECTORY_SEPARATOR,".",$path);
		}
		$this->_decoratorPath = $path;
	}
	
	/**
	 * Decorates the model with the given view and returns the HTML.
	 * @param string $viewName The name of the view file to render (without extension).
	 * @param array $data Extra data to pass to the view
	 * @return string The HTML for the view
	 */
	public function decorate($viewName = null, $data = array()) {
		if ($viewName === null) {
			$viewName = $this->viewName;
		}
		$viewAlias = $this->getDecoratorPath().".".$viewName;
		$data = CMap::mergeArray($this->extraData,$data);
		if (!isset($data['model'])) {
			$data['model'] = $this->model;
		}
		return $this->render($viewAlias,$data,true);
	}
	
}
