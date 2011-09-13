<?php
/**
 * Editds an elastic search document
 * @var AElasticSearchDocument $document the elastic search document to be edited
 * @var DocumentController $this the document controller
 */
if (isset($document->type)) {
	$this->breadcrumbs=array(
		"Elastic Search Indexes" => array("/admin/elasticSearch/index/index"),
		$document->type->index->name => array("/admin/elasticSearch/index/view","name" => $document->type->index->name),
		$document->type->name => array("/admin/elasticSearch/index/view","name" => $document->type->index->name, "type" => $document->type->name),
		"New Elastic Search Document"
	);
}
$this->beginWidget("AAdminPortlet",
				   array(
						"title" => "New Elastic Search Document",

					   ));
$this->renderPartial("_form",array("document" => $document));

$this->endWidget();
?>
