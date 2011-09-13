<?php
/**
 * Editds an elastic search document
 * @var AElasticSearchDocument $document the elastic search document to be edited
 * @var DocumentController $this the document controller
 */
$attributes = $document->detailViewAttributes();
if (isset($document->type)) {
	$this->breadcrumbs=array(
		"Elastic Search Indexes" => array("/admin/elasticSearch/index/index"),
		$document->type->index->name => array("/admin/elasticSearch/index/view","name" => $document->type->index->name),
		$document->type->name => array("/admin/elasticSearch/index/view","name" => $document->type->index->name, "type" => $document->type->name),
		$document->getName() => array("/admin/elasticSearch/document/view","index" => $document->type->index->name, "type" => $document->type->name, "id" => $document->getId()),
		"Edit ".$document->getName()
	);
}
$this->beginWidget("AAdminPortlet",
				   array(
						"title" => $document->getName(),
					   "menuItems" => (isset($document->type) ? array(
								   array(
									   "label" => "View",
									   "url" => array("/admin/elasticSearch/document/view","id" => $document->id, "type" => $document->type->name, "index" => $document->type->index->name),
								   ),
						   			array(
										"label" => "Delete",
										"url" => "#",
										'linkOptions'=>array(
											'class' => 'delete',
											'params' => array(Yii::app()->request->csrfTokenName => Yii::app()->request->getCsrfToken()),
											'submit'=>$document->createUrl("delete"),
											'confirm'=>'Are you sure you want to delete this item?'
										),
									)
							   ) : array()),
					   ));
$this->renderPartial("_form",array("document" => $document));

$this->endWidget();
?>
