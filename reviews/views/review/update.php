<?php
/**
 * An interface for adding reviews by users.
 * @var AReview $model The review model
 * @var CActiveRecord $owner The model being reviewed
 */
$this->pageTitle = "Edit a review";

echo $owner->decorate("preview");
?>
<article class='width_2'>
	<?php
	$this->renderPartial("_update",array("model" => $model, "owner" => $owner));
	?>
</article>
