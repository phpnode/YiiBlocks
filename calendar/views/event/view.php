<?php
/**
 * Displays information for a particular {@link ACalendarEvent} model
 * @var ACalendarEvent $model The ACalendarEvent model to show
 */
$this->breadcrumbs=array(
	'Acalendar Events'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'List ACalendarEvent', 'url'=>array('index')),
	array('label'=>'Create ACalendarEvent', 'url'=>array('create')),
	array('label'=>'Update ACalendarEvent', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ACalendarEvent', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ACalendarEvent', 'url'=>array('admin')),
);
?>

<h1>View ACalendarEvent #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'calendarId',
		'title',
		'content',
		'startsAt',
		'endsAt',
		'type',
		'interval',
		'parameters',
		'recurrenceEndsAt',
	),
)); ?>