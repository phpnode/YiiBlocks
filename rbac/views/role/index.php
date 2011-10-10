<?php
/**
 * Shows a list of {@link AAuthRole} models
 * @var AAuthRole $model The AAuthRole model used for searching
 */
$this->breadcrumbs=array(
	'Role Based Access Control' => array('rbac/index'),
    'Roles'
);

$this->beginWidget("AAdminPortlet",array(
									  "menuItems" => array(
										  array(
												"label" => "Create",
												"url" => array("/admin/rbac/role/create"),
											),
									),
									  "title" => "Authorisation Roles"
								   ));
?>
<p class='info box'>Roles are groups of tasks and operations that can be assigned to one or more users. Users can have many roles.</p>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'aauth-role-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
		array(
			"name" => "name",
			"value" => '$data->createLink()',
			"type" => "raw",
		),
        'description',

    ),
));

$this->endWidget();
?>
