<?php
/**
 * Allows the user to enter a new password
 * @var AUser $model the model to change the password for
 */
$this->pageTitle = "Enter A New Password";
?>
<article>
	<h1>Enter A New Password</h1>
	<p>Please enter a new password in the form below.</p>
	<div class='form'>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'user-form',
		'enableAjaxValidation'=>true,
	)); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array()); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton("Save",array("class" => "save button")); ?>
	</div>

	<?php
	$this->endWidget();
	?>
	</div>
</article>