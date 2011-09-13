<?php
/**
 * Displays an elastic search document
 * @var AElasticSearchDocument $document the elastic search document to display
 * @var DocumentController $this the document controller
 */
$attributes = $document->detailViewAttributes();
if (isset($document->type)) {
	$this->breadcrumbs=array(
		"Elastic Search Indexes" => array("/admin/elasticSearch/index/index"),
		$document->type->index->name => array("/admin/elasticSearch/index/view","name" => $document->type->index->name),
		$document->type->name => array("/admin/elasticSearch/index/view","name" => $document->type->index->name, "type" => $document->type->name),
		$document->getName()
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
#CVarDumper::dump($document,5,true);
$this->widget("zii.widgets.CDetailView",
	  array(
		  "data" => $document,
		  "attributes" => $attributes
	  ));
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
					$this->renderPartial("_element",array("data" => $item));
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
