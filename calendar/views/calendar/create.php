<?php
/**
 * A view used to create new {@link ACalendar} models
 * @var ACalendar $model The ACalendar model to be inserted
 */

$this->breadcrumbs=array(
	'Calendars'=>array('index'),
	'Create',
);
$this->layout = "//layouts/column1";
?>
<article>
<h1>Create A Calendar</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
</article>