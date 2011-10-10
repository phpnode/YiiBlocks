<?php
/**
 * A view used to update {@link AAuthOperation} models
 * @var AAuthOperation $model The AAuthOperation model to be updated
 */

$this->breadcrumbs=array(
	'Role Based Access Control' => array('rbac/index'),
	'Operations'=>array('index'),
	$model->name => array('view','slug' => $model->slug),
	'Update',
);

$this->beginWidget("AAdminPortlet",
				   array(

					  "title" => "Edit Authorisation Operation: ".$model->name,
					  "menuItems" => array(
						  array(
								"label" => "View",
								"url" => array("/admin/rbac/operation/view", "slug" => $model->slug),
							),
							array(
								"label" => "Delete",
								"url" => "#",
								'linkOptions'=>array(
									'class' => 'delete',
									'submit'=>array('delete','slug'=>$model->slug),
									'confirm'=>'Are you sure you want to delete this item?'
								),
							)
					  )
				   ));
?>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<?php
$this->endWidget();
?>