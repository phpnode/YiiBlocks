<?php
/**
 * Partial view for elastic search documents
 * @var AElasticSearchDocument $data the elastic search document
 * @var CController $this the widget
 */
$this->beginWidget("AAdminPortlet",
					array(
						"title" => $data->createLink()
					)
				);
$attributes = $data->detailViewAttributes();
$this->widget("zii.widgets.CDetailView",
	  array(
		  "data" => $data,
		  "attributes" => array_slice($attributes,0,4)
	  ));

$this->endWidget();
echo "<div class='clear'></div><br /><br />";