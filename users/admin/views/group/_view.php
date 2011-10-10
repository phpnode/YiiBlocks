<?php
/**
 * A partial view that shows information about a {@link AUserGroup} model
 * @var AUserGroup $data The AUserGroup model being rendered
 * @var integer $index the zero-based index of the data item being rendered
 * @var CListView $widget The CListView widget rendering this view
 */
?>
<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />


</div>