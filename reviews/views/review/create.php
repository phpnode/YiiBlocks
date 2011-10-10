<?php
/**
 * An interface for adding reviews by users.
 * @var AReview $model The review model
 * @var CActiveRecord $owner The model being reviewed
 */
$this->pageTitle = "Add a review";

echo $owner->decorate("preview");
?>
<article class='width_2'>
	<?php
	$this->renderPartial("_create",array("model" => $model, "owner" => $owner));
	?>
</article>
