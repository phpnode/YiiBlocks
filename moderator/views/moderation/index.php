<?php
/**
 * The administration view for the {@link Webapp} model
 * @var Webapp $model The Webapp model used for searching
 */
$this->breadcrumbs=array(
	'Moderation'=>array('/moderator'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('webapp-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Moderate Items</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'webapp-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'name' => 'ownerModel',
			'filter' => AModerationItem::listOwnerModels(),
		),
		array(
			'name' => 'ownerId',
			'value' => '$data->owner->decorate("preview")',
			'type' => 'raw'
		),
		array(
			'name' => 'status',
			'filter' => AModerationItem::$statuses,
		),
		'timeAdded:datetime',

	),
));

?>
