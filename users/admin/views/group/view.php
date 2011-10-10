<?php
/**
 * Displays information for a particular {@link AUserGroup} model
 * @var AUserGroup $model The AUserGroup model to show
 * @var AUser $user The user model used for searching
 */
$this->breadcrumbs=array(
	'User Groups'=>array('index'),
	$model->name,
);
$this->beginWidget("AAdminPortlet",
				   array(

					  "title" => "User Group: ".$model->name,

					  "menuItems" => array(
						  	array(
								"label" => "Edit",
								"url" => array("/admin/users/group/update", "id" => $model->id),
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

<?php

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
	),
));
echo "<br /><br />";
$this->beginWidget("AAdminPortlet",
				   array(

					  "title" => "Members",

					  "menuItems" => array(
						  	array(
								"label" => "Add",
								"url" => array("/admin/users/group/update", "id" => $model->id),
							),


					  )
				   ));
?>
<section class="grid_6 alpha">
<?php $this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$model->getMemberDataProvider(),
    'itemView'=>'packages.users.admin.views.user._view',
)); ?>
</section>
<section class="grid_6 alpha">
<?php $this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$user->search(),
    'itemView'=>'packages.users.admin.views.user._view',
)); ?>
</section>
<?php
$this->endWidget();

$this->endWidget();