<?php

$this->breadcrumbs=array(
	"Elastic Search Indexes" => array("/admin/elasticSearch/index/index"),
);
$this->beginWidget("AAdminPortlet",array(

									  "menuItems" => array(
										  array(
												"label" => "Create Index",
												"url" => array("/admin/elasticSearch/index/create"),
											),
									),
									  "title" => "Elastic Search Indexes"
								   ));
$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'aelasticsearch-index-grid',
		'dataProvider'=>$dataProvider,
		'columns'=>array(
			array(
				'name' => 'name',
				'header' => 'Name',
				'value' => 'CHtml::link($data->name,array("/admin/elasticSearch/index/view", "name" => $data->name))',
				'type' => 'raw',
			),
			array(
				'name' => 'totalDocuments',
				'header' => 'Total Documents',
				'type' => 'number'
			),
			array(
				'name' => 'size',
				'header' => 'Size',
			),
		),
	));
$this->endWidget();