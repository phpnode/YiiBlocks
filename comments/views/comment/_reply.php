<?php
/**
 * A view used to reply to {@link AComment Comments}
 * @var AComment $model The comment model to be inserted
 */
if (!isset($action)) {
	$action = Yii::app()->request->url;
}
?>
<article class='newComment'>
<h3>Reply</h3>
<?php

$this->renderPartial("packages.comments.views.comment._form",array("model" => $model, "action" => $action));
?>
</article>
