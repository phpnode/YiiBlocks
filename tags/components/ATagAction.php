<?php
Yii::import("packages.tags.components.*");
Yii::import("packages.tags.models.*");
class ATagAction extends CAction {
	/**
	 * The name of the model being tagged
	 * @var string
	 */
	public $modelClass;
	/**
	 * Tags a particular model
	 * @param mixed $id the PK of the model being tagged
	 * @param string $term the term to provide autocomplete suggestions for, if any
	 */
	public function run($id = null, $term = null) {
		if ($term !== null) {
			// autocomplete
			$criteria = new CDbCriteria;
			$criteria->condition = "tag LIKE :tag";
			$criteria->params[":tag"] = $term."%";
			$criteria->order = "score DESC, tag";
			$criteria->limit = 10;
			foreach(ATag::model()->findAll($criteria) as $tag) {
				echo $tag->tag."\n";
			}
			return;
		}
		if ($id === null || !Yii::app()->request->isPostRequest || !Yii::app()->request->isAjaxRequest) {
			throw new CHttpException(400, "Invalid request");
		}
		$modelClass = $this->modelClass;
		$model = $modelClass::model()->findByPk($id);
		if (!is_object($model)) {
			throw new CHttpException(404, "No such item");
		}
		$tags = $_POST['tag'];

	}
}