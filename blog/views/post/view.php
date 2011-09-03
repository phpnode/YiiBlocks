<?php
/**
 * Displays information for a particular {@link BlogPost} model
 * @uses BlogPost $model The BlogPost model to show
 */
$this->breadcrumbs=array(
	'Blog'=>array('/blog'),
	$model->title,
);
?>
<article class='blogPost'>
<?php
	$this->widget("packages.voting.widgets.AVoteButtons",array("model" => $model, "htmlOptions" => array("class" => "right")));
?>
<h1><?php echo CHtml::encode($model->title); ?></h1>
<div class='postDetails'>
	<?php
	if ($model->authorId > 0) {
		echo "By <span class='author'>".CHtml::encode($model->author->name)."</span> at ";
	}
	?>
	<span class='postTime'><?php echo Yii::app()->format->dateTime($model->timePublished ? $model->timePublished : $model->timeAdded); ?></span>
</div>
<div class='clear'></div>
<br />
<?php
	echo $model->content;
?>

</article>

