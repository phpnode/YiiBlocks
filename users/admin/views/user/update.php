<?php
/**
 * A view used to update {@link User} models
 * @var User $model The User model to be updated
 */
$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->beginWidget("AAdminPortlet",
				   array(

					  "title" => "Edit User: ".$model->name,
					  "menuItems" => array(
						  array(
								"label" => "View",
								"url" => array("/admin/users/user/view", "id" => $model->id),
							),
							array(
								"label" => "Delete",
								"url" => "#",
								'linkOptions'=>array(
									'class' => 'delete',
									'submit'=>array('delete','id'=>$model->id),
									'confirm'=>'Are you sure you want to delete this item?'
								),
							)
					  )
				   ));
?>


<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<?php
$this->endWidget();