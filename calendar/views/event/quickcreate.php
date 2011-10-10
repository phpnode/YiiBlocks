<?php
/**
 * A view used to create new {@link ACalendarEvent} models
 * @var ACalendarEvent $model The ACalendarEvent model to be inserted
 */

$this->breadcrumbs=array(
	'Events'=>array('index'),
	'Create Event',
);
$this->layout = "//layouts/column1";
?>
<h3>Event</h3>
<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'acalendar-event-form',
	'enableAjaxValidation'=>false,
)); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'startTime',array('label' => 'When')); ?>
		<?php echo $model->getTimeSummary(); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'title',array('label' => 'What')); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>250)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'content',array('label' => 'Details')); ?>
		<?php echo $form->textArea($model,'content',array('rows'=>3, 'cols'=>50)); ?>
		<?php echo $form->error($model,'content'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save',array("class" => "save button")); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->