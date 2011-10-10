<?php
/**
 * The input form for the {@link ACalendarEvent} model
 * @var ACalendarEvent $model The ACalendarEvent model
 */
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'acalendar-event-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>250)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'content'); ?>
		<?php echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'content'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'startDate'); ?>
		<?php
		$this->widget('zii.widgets.jui.CJuiDatePicker',
						array(
							"model" => $model,
							"attribute" => "startDate",
						));
		?>
		<?php echo $form->error($model,'startDate'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'startTime'); ?>
		<?php echo $form->textField($model,'startTime'); ?>
		<?php echo $form->error($model,'startTime'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'endsAt'); ?>
		<?php echo $form->textField($model,'endsAt'); ?>
		<?php echo $form->error($model,'endsAt'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->