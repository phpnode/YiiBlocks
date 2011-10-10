<?php
/**
 * A view used to update {@link ABenchmark} models
 * @var ABenchmark $model The ABenchmark model to be updated
 */
$this->breadcrumbs=array(
	'Benchmarks' => array('index'),
	$model->getUrl() => array('view','id'=>$model->id),
	'Update',
);
$this->beginWidget("AAdminPortlet",array(
									  "menuItems" => array(
										  array(
												"label" => "View",
												"url" => array("/admin/benchmark/benchmark/view", "id" => $model->id),
											),
										  array(
												"label" => "Delete",
												"url" => "#",
												'linkOptions'=>array(
													'class' => 'delete',
													'submit'=>array('delete','id'=>$model->id),
													'confirm'=>'Are you sure you want to delete this item?'
												),
											)
									),
									  "title" => "Edit Benchmark: ".$model->getUrl(),
								   ));
?>

<p class='info box'>Benchmarks can refer to either a controller route with parameters, or a static URL.</p>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

<?php
$this->endWidget();