<?php
/**
 * A view used to create new {@link AAuthRole} models
 * @var AAuthRole $model The AAuthRole model to be inserted
 */

$this->breadcrumbs=array(
	'Role Based Access Control' => array('rbac/index'),
	'Roles'=>array('index'),
	'Create',
);

$this->beginWidget("AAdminPortlet",array(
									  "title" => "Authorisation Roles"
								   ));
?>
<p class='info box'>Roles are groups of tasks and operations that can be assigned to one or more users. Users can have many roles.</p>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<?php $this->endWidget(); ?>