<?php
/**
 * The input form for the {@link AUserGroup} model
 * @var AUserGroup $model The AUserGroup model
 */
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'auser-group-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>250)); ?>
		<p class='hint'>Please enter a name for the group</p>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array("class" => "save button")); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->