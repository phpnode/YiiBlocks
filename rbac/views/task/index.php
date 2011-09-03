<?php
/**
 * Shows a list of {@link AAuthTask} models
 * @uses AAuthTask $model The AAuthTask model used for searching
 */
$this->breadcrumbs=array(
	'Role Based Access Control' => array('rbac/index'),
    'Tasks'
);

$this->beginWidget("AAdminPortlet",array(
									  "menuItems" => array(
										  array(
												"label" => "Create",
												"url" => array("/admin/rbac/task/create"),
											),
									),
									  "title" => "Authorisation Tasks"
								   ));
?>
<p class='info box'>Tasks are groups of operations that can be assigned to one or more roles.</p>
	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'aauth-task-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'columns'=>array(
			array(
				"name" => "name",
				"value" => 'CHtml::link($data->name,array("view","slug" => $data->slug))',
				"type" => "raw",
			),
			'description',

		),
	));
$this->endWidget();
?>