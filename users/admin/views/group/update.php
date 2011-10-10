<?php
/**
 * A view used to update {@link AUserGroup} models
 * @var AUserGroup $model The AUserGroup model to be updated
 */
$this->breadcrumbs=array(
	'Auser Groups'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List AUserGroup', 'url'=>array('index')),
	array('label'=>'Create AUserGroup', 'url'=>array('create')),
	array('label'=>'View AUserGroup', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage AUserGroup', 'url'=>array('admin')),
);
?>

<h1>Update AUserGroup <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>