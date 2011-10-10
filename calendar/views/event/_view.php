<?php
/**
 * A partial view that shows information about a {@link ACalendarEvent} model
 * @var ACalendarEvent $data The ACalendarEvent model being rendered
 * @var integer $index the zero-based index of the data item being rendered
 * @var CListView $widget The CListView widget rendering this view
 */
?>
<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('calendarId')); ?>:</b>
	<?php echo CHtml::encode($data->calendarId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('title')); ?>:</b>
	<?php echo CHtml::encode($data->title); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('content')); ?>:</b>
	<?php echo CHtml::encode($data->content); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('startsAt')); ?>:</b>
	<?php echo CHtml::encode($data->startsAt); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('endsAt')); ?>:</b>
	<?php echo CHtml::encode($data->endsAt); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
	<?php echo CHtml::encode($data->type); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('interval')); ?>:</b>
	<?php echo CHtml::encode($data->interval); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('parameters')); ?>:</b>
	<?php echo CHtml::encode($data->parameters); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('recurrenceEndsAt')); ?>:</b>
	<?php echo CHtml::encode($data->recurrenceEndsAt); ?>
	<br />

	*/ ?>

</div>