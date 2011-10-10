<?php
/**
 * Displays information for a particular {@link User} model
 * @var User $model The User model to show
 */
$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->name,
);
$this->beginWidget("AAdminPortlet",
				   array(

					  "title" => "View User: ".$model->name,
					  "sidebarMenuItems" => array(
						  array(
							  "label" => "User Details",
							  "url" => array("/admin/users/user/view", "id" => $model->id),
						  ),
						  array(
							  "label" => "Groups",
							  "url" => array("/admin/users/user/groups", "id" => $model->id),
						  ),
						  array(
							  "label" => "Roles",
							  "url" => array("/admin/users/user/roles", "id" => $model->id),
						  ),
					  ),
					  "menuItems" => array(
						  	array(
								"label" => "Edit",
								"url" => array("/admin/users/user/update", "id" => $model->id),
							),
						  	array(
								"label" => "Impersonate",
								"url" => "#",
								'linkOptions'=>array(
									'submit'=>array('impersonate','id'=>$model->id),
									'confirm'=>'Are you sure you want to impersonate this user? You will be logged out of your account and will have to log back in to access the admin section.'
								),
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
<?php echo CHtml::encode($model->name)." belongs to ".$model->totalGroups." group(s)"; ?>
<?php

$this->endWidget();