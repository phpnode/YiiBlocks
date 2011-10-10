<?php
/**
 * A view used to create new {@link ACalendarEvent} models
 * @var ACalendarEvent $model The ACalendarEvent model to be inserted
 */

$this->breadcrumbs=array(
	'Events'=>array('index'),
	'Create Event',
);
$this->layout = "//layouts/column1";
?>
<article>
<h1>Create An Event</h1>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
</article>