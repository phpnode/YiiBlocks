<?php
/**
 * An interface for creating new packages
 * @var APackage $model The package to create
 */
$this->beginWidget("AAdminPortlet",
				   array(
					  "title" => "Create a Yii Package",
			   ));
$this->renderPartial("_create",array("model" => $model));
$this->endWidget();
