<?php
/**
 * Review Preview, shows a review.
 * @var AReview $model the review model
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
	// display the moderation buttons
	$this->widget("packages.moderator.widgets.AModerationButtons",array(
		"model" => $model,
		"htmlOptions" => array("class" => "right")
	));
	// display the customised voting buttons
	$this->widget("packages.voting.widgets.AVoteButtons",array(
		"model" => $model,
		"upvoteClass" => "upvote",
		"downvoteClass" => "downvote",
		"template" => "Did you find this review helpful? {upvote} / {downvote}<br />{summary}",
		"summaryTemplate" => "<span class='score'>{score}  point(s)</span>",
		"upvoteLabel" => "Yes",
		"downvoteLabel" => "No",
		"upvotedLabel" => "<strong>Yes</strong>",
		"downvotedLabel" => "<strong>No</strong>",

	));

	?>
</article>
