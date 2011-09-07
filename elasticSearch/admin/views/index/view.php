<?php
/**
 * Displays information about a particular elastic search index
 * @var AElasticSearchIndex $model The elastic search index
 * @var AElasticSearchDocumentType $type The selected elastic search document type
 * @var IndexController $this the index controller
 */
$this->breadcrumbs=array(
	"Elastic Search Indexes" => array("/admin/elasticSearch/index/index"),
	$model->name,
);
$this->beginWidget("AAdminPortlet",
				   		array(
							"menuItems" => array(
								array(
									"label" => "Edit",
									"url" => array("/admin/elasticSearch/index/update", "name" => $model->name),
								),
						),
							"title" => "Elastic Search Index: ".$model->name
					   ));
$this->widget("zii.widgets.CDetailView",
			  array(
				  "data" => $model,
				  "attributes" => array(
					  "name",
					  "totalDocuments:number",
					  "size",

				  )
			  ));
echo "<br />";
$sidebarMenuItems = array();
foreach($model->getTypes() as $docType) {
	$sidebarMenuItem = array(
		"label" => $docType->name,
		"url" => array("/admin/elasticSearch/index/view","name" => $model->name, "type" => $docType->name),
	);
	if (is_object($type) && $type->name == $docType->name) {
		$sidebarMenuItem['active'] = true;
	}
	$sidebarMenuItems[] = $sidebarMenuItem;

}

$this->beginWidget("AAdminPortlet",
				   array(
						"title" => "Document Types",
						"sidebarMenuItems" => $sidebarMenuItems
					   ));
if (is_object($type)) {
	$this->beginWidget("AAdminPortlet",
				   array(
						"title" => $type->name,
					   ));
	$this->renderPartial("/document/_search",array("type" => $type));
	$this->widget('zii.widgets.CListView', array(
		'id'=>'aelasticsearch-index-type-'.$type->name.'-grid',
		'dataProvider'=>$type->dataProvider,
		'itemView' => "/document/_view"
	));
	$this->endWidget();
}
$this->endWidget();
$this->endWidget();