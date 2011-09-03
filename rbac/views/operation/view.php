<?php
/**
 * Displays information for a particular {@link AAuthOperation} model
 * @uses AAuthOperation $model The AAuthOperation model to show
 */
$this->breadcrumbs=array(
	'Role Based Access Control' => array('rbac/index'),
	'Operations'=>array('index'),
	$model->name,
);

Yii::app()->clientScript->registerCoreScript("jquery-ui");
$this->beginWidget("AAdminPortlet",
				   array(

					  "title" => "Authorisation Operation: ".$model->name,
					  "menuItems" => array(
						  array(
								"label" => "Edit",
								"url" => array("/admin/rbac/operation/update", "slug" => $model->slug),
							),
							array(
								"label" => "Delete",
								"url" => "#",
								'linkOptions'=>array(
									'class' => 'delete',
									'submit'=>array('delete','slug'=>$model->slug),
									'confirm'=>'Are you sure you want to delete this item?'
								),
							)
					  )
				   ));
?>
<section class='grid_6 alpha'>

<?php

$this->widget("zii.widgets.CDetailView",
			  array(
				  "data" => $model,
				  "attributes" => array(
					  "name",
					  "description",
					  array(
						  "name" => "bizrule",
						  "type" => "raw",
						  "value" => $model->formatBizRule(),
					  )
				  )
			  ));
?>
</section>
<?php
$this->beginWidget("AAdminPortlet",
				   array(
						"title" => "Access Summary",
						"htmlOptions" => array("class" => "grid_6 omega"),
					   ));
echo "<ul>";
echo $model->summary;
echo "</ul>";
$this->endWidget();

$this->endWidget();
?>

