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
						"sidebarMenuItems" => $sidebarMenuItems,
					   ));
if (is_object($type)) {
	$this->beginWidget("AAdminPortlet",
				   array(
						"title" => $type->name,
					    "menuItems" => array(
							array(
								"label" => "Create Document",
								"url" => array("document/create","index" => $model->name, "type" => $type->name)
							),
							array(
										"label" => "Delete All",
										"url" => "#",
										'linkOptions'=>array(
											'class' => 'delete',
											'params' => array(Yii::app()->request->csrfTokenName => Yii::app()->request->getCsrfToken()),
											'submit'=>array("deleteType","name" => $model->name, "type" => $type->name),
											'confirm'=>'Are you sure you want to delete this document type? All documents in this type will be deleted!'
										),
									)
						)
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