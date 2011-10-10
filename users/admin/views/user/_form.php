<?php
/**
 * The input form for the {@link AUser} model
 * @var AUser $model The User model
 */
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>true,
)); ?>


	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>50,'maxlength'=>50)); ?>
		<p class='hint'>Enter the user's name.</p>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>45,'maxlength'=>45)); ?>
		<p class='hint'>Enter a password for the user.</p>
		<?php echo $form->error($model,'password'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>450)); ?>
		<p class='hint'>Enter the user's email address</p>
		<?php echo $form->error($model,'email'); ?>
	</div>
<?php
if (Yii::app()->getModule("users")->enableProfileImages) {
?>
	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>450)); ?>
		<p class='hint'>Enter the user's email address</p>
		<?php echo $form->error($model,'email'); ?>
	</div>
<?php
}
?>
	<div class="row">
		<?php echo $form->labelEx($model,'requiresNewPassword'); ?>
		<?php echo $form->checkbox($model,'requiresNewPassword'); ?>
		<p class='hint'>Check this box to require the user to change their password on next login.</p>
		<?php echo $form->error($model,'requiresNewPassword'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array("class" => "save button")); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->