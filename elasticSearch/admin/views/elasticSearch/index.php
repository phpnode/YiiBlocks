<?php
/**
 * @var CArrayDataProvder $dataProvider the data provider containing the indices
 * @var AElasticSearch $elasticSearch the elastic search component
 */
$elasticSearch = Yii::app()->elasticSearch;
$this->beginWidget("AAdminPortlet",array(
									  "title" => "Elastic Search",
									  "htmlOptions" => array(
										  "class" => "grid_12 alpha omega"
									  )
								   ));
?>
<p class='info box'>
	Elastic Search is a free, distributed, schemaless search solution built on top of Lucene.
	With Elastic Search you can easily index and search millions of documents in real time.
</p>
<?php
$this->beginWidget("AAdminPortlet",array(
									  "title" => "Indices",
									  "htmlOptions" => array(
										  "class" => "grid_12 alpha omega"
									  )
								   ));
?>

<section class='grid_6 alpha'>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'aelasticsearch-index-grid',
		'dataProvider'=>$dataProvider,
		'columns'=>array(
			array(
				'name' => 'name',
				'header' => 'Index Name',
				'value' => 'CHtml::link($data->name,array("/admin/elasticSearch/index/view", "name" => $data->name))',
				'type' => 'raw',
			),
			array(
				'name' => 'totalDocuments',
				'header' => 'Total Documents',
				'type' => 'number'
			),
			array(
				'name' => 'size',
				'header' => 'Size',
			),
		),
	));
?>

</section>
<section class='grid_6 omega'>
<?php
$sizeData = array();
$totalDocsData = array();
$seriesLabels = array();
foreach($elasticSearch->getIndices() as $name => $index) {
	$sizeData[] = $index->sizeInMegaBytes;
	$totalDocsData[] = $index->totalDocuments;
	$seriesLabels[] = $name;

}

$this->widget("packages.plotcharts.APlotChartWidget",
				array(
					"data" => array($sizeData),
					"plugins" => array(
						"barRenderer",
						"categoryAxisRenderer"
					),
					"options" => array(
						"title" => "Index Size (On Disk)",
						"seriesDefaults" => array(
							"renderer" => "js:jQuery.jqplot.BarRenderer",
							"yaxis" => "y2axis"
						),
						"axes" => array(
							"xaxis" => array(
								"ticks" => $seriesLabels,
								"renderer" => "js:jQuery.jqplot.CategoryAxisRenderer",

							),
							"y2axis" => array(
								"tickOptions" => array(
									"formatString" => "%'10d MB"
								),
							)
						)

					)
				));

echo "<br /><hr /><br />";
$this->widget("packages.plotcharts.APlotChartWidget",
				array(
					"data" => array($totalDocsData),
					"plugins" => array(
						"barRenderer",
						"categoryAxisRenderer"
					),
					"options" => array(
						"title" => "Index Size (Total Documents)",
						"seriesDefaults" => array(
							"renderer" => "js:jQuery.jqplot.BarRenderer",
							"yaxis" => "y2axis"
						),
						"axes" => array(
							"xaxis" => array(
								"ticks" => $seriesLabels,
								"renderer" => "js:jQuery.jqplot.CategoryAxisRenderer",

							),
							"y2axis" => array(
								"tickOptions" => array(
									"formatString" => "%'10d"
								),
							)
						)

					)
				));
?>
</section>

<?php
$this->endWidget();
$cluster = Yii::app()->elasticSearch->cluster;
$this->beginWidget("AAdminPortlet",array(
									  "title" => "Cluster: ".$cluster->name,
									  "htmlOptions" => array(
										  "class" => "grid_6 omega"
									  )
								   ));

?>
<p class="info box">There <?php echo ((($nodeCount = count($cluster->nodes)) == 1) ? "is 1 node" : "are $nodeCount nodes"); ?> in this cluster</p>
<div class='grid-view'>
	<br />
	<table class='items'>
		<tr>
			<th>Name</th>
			<th>ID</th>
			<th>Transport Address</th>
		</tr>
		<?php
		foreach($cluster->nodes as $id => $node) {
			echo "<tr>";
			echo "<td>$node->name</td>\n";
			echo "<td>$id".($id == $cluster->masterNode ? " (master)" : "")."</td>";
			echo "<td>$node->transport_address</td>\n";
			echo "</tr>";
		}
		?>
	</table>
</div>
<?php
$this->endWidget();
$this->endWidget();
?>

