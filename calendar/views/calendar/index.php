<?php
/**
 * Shows a list of {@link ACalendar} models
 * @var CActiveDataProvider $dataProvider The dataProvider containing ACalendar data
 */

$this->breadcrumbs=array(
	'Acalendars',
);

$this->menu=array(
	array('label'=>'Create ACalendar', 'url'=>array('create')),
	array('label'=>'Manage ACalendar', 'url'=>array('admin')),
);
?>

<h1>Acalendars</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>