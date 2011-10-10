<?php
/**
 * A view used to update {@link ACalendar} models
 * @var ACalendar $model The ACalendar model to be updated
 */
$this->breadcrumbs=array(
	'Acalendars'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ACalendar', 'url'=>array('index')),
	array('label'=>'Create ACalendar', 'url'=>array('create')),
	array('label'=>'View ACalendar', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ACalendar', 'url'=>array('admin')),
);
?>

<h1>Update ACalendar <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>