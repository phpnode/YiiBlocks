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
		$document->getName() => array("/admin/elasticSearch/index/view","name" => $document->type->index->name, "type" => $document->type->name, "id" => $document->getId()),
		"Edit ".$document->getName()
	);
}
$this->beginWidget("AAdminPortlet",
				   array(
						"title" => $document->getName(),
					   "menuItems" => (isset($document->type) ? array(
								   array(
									   "label" => "Edit",
									   "url" => array("/admin/elasticSearch/document/update","id" => $document->id, "type" => $document->type->name, "index" => $document->type->index->name),
								   ),
						   			array(
									   "label" => "Delete",
									   "url" => array("/admin/elasticSearch/document/update","id" => $document->id, "type" => $document->type->name, "index" => $document->type->index->name),
								   ),
							   ) : array()),
					   ));
$this->renderPartial("_form",array("document" => $document));
echo "<br /><br />";
$n = 0;
foreach($document as $name => $element) {
	if (is_array($element)) {
		if (count($element)) {
			$this->beginWidget("AAdminPortlet",
						   array(
							   "title" => $name,
							   "htmlOptions" => array(
								   "class" => "grid_6 ".($n % 2 ? "omega" : "alpha"),
							   ),
						   ));

			foreach($element as $item) {
				if ($item instanceof AElasticSearchDocument) {
					$this->renderPartial("_form",array("document" => $item));
				}
				else {
					echo $item."<br />";
				}
			}
			$n++;
			$this->endWidget();
		}
	}
}
$this->endWidget();
?>
