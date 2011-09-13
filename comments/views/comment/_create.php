<?php
/**
 * A view used to create new {@link AComment Comments}
 * @var AComment $model The comment model to be inserted
 */
if (!isset($action)) {
	$action = Yii::app()->request->url;
}
?>
<article class='newComment'>
<h3>Post A Comment</h3>
<?php

$this->renderPartial("packages.comments.views.comment._form",array("model" => $model, "action" => $action));
?>
</article>
