<?php
/**
 * Shows a list of documents in a particular index
 * @var DocumentController $this The controller
 * @var AElasticSearchDataProvider $dataProvider the data provider containing the documents
 * @var AElasticSearchDocumentType $type the document type
 * @var AElasticSearchIndex $index the elastic search index
 */
$this->breadcrumbs=array(
	"Elastic Search Indexes" => array("/admin/elasticSearch/index/index"),
	$type->index->name,
);
$this->beginWidget("AAdminPortlet",
				   		array(
							"menuItems" => array(
								array(
									"label" => "Create",
									"url" => array("/admin/elasticSearch/document/create", "index" => $index->name, "type" => $type->name),
								),
						),
							"title" => "Elastic Search Documents in ".$index->name ." / ".$type->name,
					   ));
$this->renderPartial("/document/_search",array("type" => $type));
	$this->widget('zii.widgets.CListView', array(
		'id'=>'aelasticsearch-index-type-'.$type->name.'-grid',
		'dataProvider'=>$type->dataProvider,
		'itemView' => "/document/_view"
	));

$this->endWidget();