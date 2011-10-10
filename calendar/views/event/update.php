<?php
/**
 * A view used to update {@link ACalendarEvent} models
 * @var ACalendarEvent $model The ACalendarEvent model to be updated
 */
$this->breadcrumbs=array(
	'Acalendar Events'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ACalendarEvent', 'url'=>array('index')),
	array('label'=>'Create ACalendarEvent', 'url'=>array('create')),
	array('label'=>'View ACalendarEvent', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ACalendarEvent', 'url'=>array('admin')),
);
?>

<h1>Update ACalendarEvent <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>