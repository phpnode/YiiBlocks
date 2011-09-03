<?php
/**
 * A partial view that shows information about a {@link BlogPost} model
 * @uses BlogPost $data The BlogPost model being rendered
 * @uses integer $index the zero-based index of the data item being rendered
 * @uses CListView $widget The CListView widget rendering this view
 */
?>
<article class="blogPost">

	<h2><?php echo $data->createLink(); ?></h2>
	<div class='postDetails'>
		<?php
		if ($data->authorId > 0) {
			echo "By <span class='author'>".CHtml::encode($data->author->name)."</span> at ";
		}
		?>
		<span class='postTime'><?php echo Yii::app()->format->dateTime($data->timePublished ? $data->timePublished : $data->timeAdded); ?></span>
	</div>
	<?php echo CHtml::encode($data->summary); ?>
	<?php
	echo $data->createLink("Read More");
	?>
</article>