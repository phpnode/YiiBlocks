<?php
/**
 * A view used to create new {@link ABenchmark} models
 * @var ABenchmark $model The ABenchmark model to be inserted
 */

$this->breadcrumbs=array(
	'Benchmarks' => array('index'),
	'Create',
);
$this->beginWidget("AAdminPortlet",array(
									  "title" => "Create Benchmark",
								   ));
?>

<p class='info box'>Benchmarks can refer to either a controller route with parameters, or a static URL.</p>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

<?php
$this->endWidget();