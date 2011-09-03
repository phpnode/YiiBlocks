<?php
/**
 * Shows a list of {@link AAuthRole} models
 * @uses AAuthRole $model The AAuthRole model used for searching
 */
$this->breadcrumbs=array(
	'Role Based Access Control' => array('rbac/index'),
    'Operations'
);

$this->beginWidget("AAdminPortlet",array(
									  "menuItems" => array(
										  array(
												"label" => "Create",
												"url" => array("/admin/rbac/operation/create"),
											),
									),
									  "title" => "Authorisation Operations"
								   ));
?>
<p class='info box'>Operations are the lowest level in the authorisation hierarchy, they can be assigned to tasks or directly to roles</p>
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
