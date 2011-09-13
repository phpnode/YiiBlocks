<?php

$this->breadcrumbs=array(
	"Database Tables" => array("/admin/dbmanager/table/index"),
);
$this->beginWidget("AAdminPortlet",array(

									  "menuItems" => array(
										  array(
												"label" => "Create Table",
												"url" => array("/admin/dbmanager/table/create"),
											),
									),
									  "title" => "Database Tables"
								   ));
$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'atable-model-grid',
		'dataProvider'=>$dataProvider,
		'columns'=>array(
			array(
				'name' => 'name',
				'header' => 'Name',
				'value' => 'CHtml::link($data->name,array("/admin/dbmanager/table/view", "name" => $data->name))',
				'type' => 'raw',
			),
			array(
				'name' => 'totalRows',
				'header' => 'Total Rows',
				'type' => 'number'
			),

		),
	));
$this->endWidget();