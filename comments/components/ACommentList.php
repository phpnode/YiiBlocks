<?php
Yii::import("zii.widgets.*");
Yii::import("packages.voting.widgets.*");
/**
 * ACommentList is a widget that shows a list of comments
 * for a model and displays a form so that the user can add a new comment.
 * @package packages.comments.components
 * @author Charles Pick
 */
class ACommentList extends CListView {

	/**
	 * @var string the view used for rendering each data item.
	 * @see CListView::$itemView
	 */
	public $itemView = "packages.comments.views.comment._view";

	/**
	 * @var string the view used for rendering the comment form.
	 */
	public $formView = "packages.comments.views.comment._create";


	/**
	 * @var boolean whether to show the comment form or not.
	 */
	public $showForm = true;

	/**
	 * The model that the comments belong to
	 * @var CActiveRecord
	 */
	public $model;


	/**
	 * The comment to add
	 * @var Comment
	 */
	public $comment;

	/**
	 * @var string the template to be used to control the layout of various components in the list view.
	 * These tokens are recognized: {form}, {summary}, {sorter}, {items} and {pager}. They will be replaced with the
	 * summary text, the sort links, the add comment form, the data item list, and the pager.
	 */
	public $template="{form}\n{sorter}\n{items}\n{pager}";


	/**
	 * Initializes the widget
	 */
	public function init() {
		if ($this->dataProvider === null) {
			$this->dataProvider = new CArrayDataProvider($this->model->getCommentTree(),array(
				"pagination" => false,
				"sort" => false,
			));

		}
		if ($this->comment === null) {
			$this->comment = new AComment;
		}

		return parent::init();
	}

	/**
	 * Displays the widget and registers the JavaScript.
	 */
	public function run() {
		$postData = array(
			Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken
		);
		$postDataEncoded = CJavaScript::encode($postData);
		$script = <<<JS
$("#{$this->id} a.reply").live("click", function (e) {
	var replyDiv = $(this).parents("div.comment").first().find("div.reply").first();
	if ($(replyDiv).html().length === 0) {
		$.ajax({
			url: $(this).attr("href"),
			data: {$postDataEncoded},
			type: 'POST',
			success: function (html) {
				$(replyDiv).html(html);
			}
		});
	}
	e.preventDefault();
});
JS;
		if (Yii::app()->getModule("comments")->votableComments) {
			$userIsLoggedIn = (bool) !Yii::app()->user->isGuest;
			$voteOptions = CJavaScript::encode(array("postData" => $postData, "userIsLoggedIn" => $userIsLoggedIn));
			$script = <<<JS
{$script}
$("#{$this->id} div.voteButtons").voteButtons({$voteOptions});
JS;
			AVoteButtons::registerScripts();

		}

		$script = <<<JS
{$script}
$("#{$this->id} a.collapsible").bind("click", function (e) {
	$(this).parents(".collapsible").first().toggleClass("collapsed");
	e.preventDefault();
});
JS;
		Yii::app()->clientScript->registerScript(get_class($this)."#links",$script);
		parent::run();
	}

	/**
	 * Renders the comment form
	 */
	public function renderForm() {
		if (!$this->showForm) {
			return;
		}

		$controller = $this->getController();
		$action = array("/comments/comment/create");
		$action['ownerModel'] = get_class($this->model);

		$action['ownerId'] = $this->model->primaryKey;
		$controller->renderPartial($this->formView,array("model" => $this->comment, "action" => $action, "owner" => $this->model));

	}
}
