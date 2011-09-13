<?php
/**
 * A partial view that shows information about a {@link AComment} model
 * @var AComment $data The Comment model being rendered
 * @var integer $index the zero-based index of the data item being rendered
 * @var ACommentList $widget The ACommentList widget rendering this view
 */

$module = Yii::app()->getModule("comments");
$replyUrl = array("/comments/comment/create");
$replyUrl["ownerModel"] = $data->ownerModel;
$replyUrl["ownerId"] = $data->ownerId;
$replyUrl["replyToId"] = $data->id;
?>
<article class="comment collapsible <?php
echo ($index  % 2 ? "even" : "odd");
if ($module->votableComments && $data->totalVoteScore < 1) {
	echo " collapsed";
}
?>" id="comment-<?php echo $data->id; ?>">
	 <?php
    if ($module->votableComments) {
       	// todo: do something!
	}
    ?>
    <div class="author">
        <?php echo CHtml::encode($data->authorName); ?>
        <a class='collapsible' href='#'> </a>
    </div>

    <div class='timeAdded'>
    	<?php echo Yii::app()->format->dateTime($data->timeAdded); ?>
    </div>
	<div class='text'>
    	<?php echo nl2br(CHtml::encode($data->content)); ?>
	</div>
	<div class='actions'>
		<?php
		 if ($module->allowReplies && ($module->nestLimit === null || $data->level <= $module->nestLimit)) {

			 echo CHtml::link("Reply",$replyUrl,array("class" => "icon reply"));
		}

		?>
	</div>
	<div class='reply'></div>
	<?php

	if ($module->allowReplies && count($replies = $data->replies)) {
		?>
		<div class='replies'>
		<?php
		foreach($replies as $n => $reply) {
			$this->renderPartial("packages.comments.views.comment._view",array("data" => $reply, "index" => $n));
		}
		?>
		</div>
		<?php
	}

	?>
</article>
<?php

