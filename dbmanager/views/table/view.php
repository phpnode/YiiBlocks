<?php
/**
 * Displays a given table
 * @var ATableModel $model the table model
 * @var TableController $this the table controller
 * @var ATableRowModel $rowFilter the table row model used for filtering
 */
$this->breadcrumbs=array(
	"Database Tables" => array("/admin/dbmanager/table/index"),
	$model->name,
);
$this->beginWidget("AAdminPortlet",
				   		array(
							"title" => "Database Table: ".$model->name
					   ));
$this->widget("zii.widgets.CDetailView",
			  array(
				  "data" => $model,
				  "attributes" => array(
					  "name",
					  "totalRows:number",

				  )
			  ));
echo "<br />";
$dataProvider = $rowFilter->search();
$dataProvider->getPagination()->setPageSize(30);
$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'atable-row-model-grid',
		'dataProvider'=>$dataProvider,
		'filter' => $rowFilter,
		'columns' => $rowFilter->getGridColumns(),
		'ajaxUpdate' => false,
		'htmlOptions' => array(
			'style' => 'overflow-x:auto;',
		)
	));
$this->endWidget();