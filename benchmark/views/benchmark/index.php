<?php
/**
 * The administration view for the {@link ABenchmark} model
 * @var ABenchmark $model The ABenchmark model used for searching
 */
$this->breadcrumbs=array(
	'Benchmarks'
);

$this->menu=array(
	array('label'=>'List ABenchmark', 'url'=>array('index')),
	array('label'=>'Create ABenchmark', 'url'=>array('create')),
);

$this->beginWidget("AAdminPortlet",array(
									  "menuItems" => array(
										  array(
												"label" => "Create",
												"url" => array("/admin/benchmark/benchmark/create"),
											),
									),
									  "title" => "Benchmarks"
								   ));
?>
<p class='info box'>Benchmarks help ensure that changes you make to your application or server don't adversely affect performance.</p>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'abenchmark-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        array(
			"value" => '$data->createLink($data->getUrl(),null,array("class" => ($data->isRegression ? "warning icon" : ($data->isProgression ? "tick icon" : ""))))',
			"name" => "url",
			"header" => "URL",
			"type" => "raw",
		),
		"lastResult.requestsPerSecond:number",
		array(
			"value" => 'round($data->difference,2)."&#37;;"',
			"type" => "raw",
			"header" => "Performance Difference",

		),
		array(
			'value' => '"<span class=\"sparkline\">".implode(",",$data->getSparklineData())."</span>"',
			'type' => 'raw',
			"header" => "Performance History",
		),
        array(
            'class'=>'CButtonColumn',
        ),
    ),
));

Yii::createComponent(array(
						 "class" => "packages.sparklines.ASparklineWidget"
			  ))->registerScripts();
Yii::app()->clientScript->registerScript("benchmarkSparklines","$('.sparkline').sparkline('html', {width: '150px', chartRangeMin: 0});");
$this->endWidget();
phpinfo();
?>