<?php
/**
 * The administration view for the {@link AUserGroup} model
 * @var AUserGroup $model The AUserGroup model used for searching
 */
$this->breadcrumbs=array(
	'User Groups'
);
$this->beginWidget("AAdminPortlet",array(
									  "menuItems" => array(
										  array(
												"label" => "Create",
												"url" => array("/admin/users/group/create"),
											),
									),
									  "title" => "User Groups"
								   ));
?>


<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'auser-group-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		array(
			'class'=>'CButtonColumn',
		),
	),
));

$this->endWidget();