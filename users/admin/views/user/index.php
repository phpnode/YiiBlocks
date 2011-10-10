<?php
/**
 * The administration view for the {@link AUser} model
 * @var AUser $model The User model used for searching
 */
$this->breadcrumbs=array(
	'Users'
);
$this->beginWidget("AAdminPortlet",array(
									  "menuItems" => array(
										  array(
												"label" => "Create",
												"url" => array("/admin/users/user/create"),
											),
									),
									  "title" => "Users"
								   ));
?>

<p class='info box'>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>
<?php $this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$model->search(),
    'itemView'=>'_view',
)); ?>
<?php

/*$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'name',
		'id',
		'email',
		array(
			'class'=>'CButtonColumn',
		),
	),
));*/
?>

<?php
$this->endWidget();