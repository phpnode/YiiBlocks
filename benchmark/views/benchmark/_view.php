<?php
/**
 * A partial view that shows information about a {@link ABenchmark} model
 * @var ABenchmark $data The ABenchmark model being rendered
 * @var integer $index the zero-based index of the data item being rendered
 * @var CListView $widget The CListView widget rendering this view
 */
?>
<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('url')); ?>:</b>
	<?php echo CHtml::encode($data->url); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('route')); ?>:</b>
	<?php echo CHtml::encode($data->route); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('params')); ?>:</b>
	<?php echo CHtml::encode($data->params); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timeAdded')); ?>:</b>
	<?php echo CHtml::encode($data->timeAdded); ?>
	<br />


</div>