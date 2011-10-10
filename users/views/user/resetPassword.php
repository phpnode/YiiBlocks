<?php
/**
 * Displays a password reset form
 * @var AUser $model the model to reset the password for
 */
$this->pageTitle = "Reset Your Password";
?>
<article>
	<h1>Reset Your Password</h1>
	<p>Please enter your email address in the box below, and we'll send you a link to reset your password.</p>
	<div class='form'>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'user-form',
		'enableAjaxValidation'=>true,
	)); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>450)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton("Reset Password",array("class" => "button")); ?>
	</div>

	<?php
	$this->endWidget();
	?>
	</div>
</article>