<?php
/**
 * Displays the contents of a webdav directory
 */
$breadcrumbs = array(
	"/" => $this->createUrl("/".Yii::app()->controller->route),
);
$parts = array();
foreach(explode("/",$path) as $part) {
	$parts[] = $part;
	$breadcrumbs[$part] = $this->createUrl("/".Yii::app()->controller->route).implode("/",$parts);
}
$this->breadcrumbs = $breadcrumbs;
?>
<?php
$this->beginWidget("AAdminPortlet",
				   array(
					  "menuItems" => array(
						  array(
								"label" => "Create Folder",
								"url" => array("/".Yii::app()->controller->route),
							  	"linkOptions" => array(
									  "id" => "createFolderLink"
								  ),
							),
							array(
								"label" => "Upload File",
								"url" => array("/".Yii::app()->controller->route),
							  	"linkOptions" => array(
									"id" => "uploadFileLink"
								),
							),
					),
					  "title" => "Webdav: /".$path,
			   ));
$this->widget("zii.widgets.grid.CGridView",
			  array(
				   "dataProvider" => $dataProvider,
				   "columns" => array(
					   array(
							'class' => 'CCheckBoxColumn',
							'selectableRows' => 2,
							'id' => 'Post',
						),
						array(
							"name" => "name",
							"value" => '$data["link"]',
							"header" => "Name",
							'type' => 'raw',
						),
						array(
							"name" => "size",
							"header" => "Size",
							"value" => '$data["size"] != 0 ? AFileHelper::formatSize($data["size"]) : null',
						),
						array(
							"name" => "lastModified",
							"header" => "Last Modified",
							"value" => '$data["lastModified"] === null ? null : Yii::app()->format->dateTime(strtotime($data["lastModified"]))'
						),
				   ),
				));
$this->endWidget();
?>