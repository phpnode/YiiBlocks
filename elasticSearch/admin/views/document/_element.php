<?php
/**
 * Partial view for elastic search document elements
 * @var AElasticSearchDocument $data the elastic search document element
 * @var CController $this the widget
 */
$this->beginWidget("AAdminPortlet",
					array(
						"title" => $data->getName()
					)
				);
$attributes = $data->detailViewAttributes();
$this->widget("zii.widgets.CDetailView",
	  array(
		  "data" => $data,
		  "attributes" => $attributes
	  ));

$this->endWidget();
echo "<div class='clear'></div><br /><br />";