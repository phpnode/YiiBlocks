<?php
/**
 * Review Preview, shows a review.
 * @uses AReview $model the review model
 */
?>
<article class='width_2 review'>
	<?php
	if ($model->title != "") {
		echo "<h1>".CHtml::encode($model->title)."</h1>";
	}
	?>
	<?php
	echo nl2br(CHtml::encode($model->content));
	?>
	<br />
	<strong>Rating:</strong> <span class='rating'><?php
	echo $model->score;
	?></span>
	<hr />
	<?php
	echo "by ";
	if ($model->reviewerId) {
		echo $model->reviewer->createLink($model->reviewer->getName(),null,null,array("class" => "reviewer"));
	}
	else {
		echo "<span class='reviewer'>Anonymous</span>";
	}
	echo " at ".Yii::app()->format->formatDateTime($model->timeAdded);
	?>
</article>
