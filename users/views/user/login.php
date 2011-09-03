<?php
/**
 * Displays a form to allow the user to login
 * @uses ALoginForm $model The login form model
 */
?>
<article>
	<h1>Login to your account</h1>
	<p>Please fill out the form below to login to your account</p>
	<div class='form'>
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'user-form',
			'enableAjaxValidation'=>true,
		)); ?>
		
		<div class="row">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>450)); ?>
			<p class='hint'>Please enter your email address.</p>
			<?php echo $form->error($model,'email'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>450)); ?>
			<p class='hint'>Please enter your password.</p>
			<?php echo $form->error($model,'password'); ?>
		</div>
		<div class="row rememberMe">
			<?php echo $form->checkBox($model,'rememberMe'); ?>
			<?php echo $form->label($model,'rememberMe'); ?>
			<?php echo $form->error($model,'rememberMe'); ?>
		</div>
	
		<div class="row buttons">
			<?php echo CHtml::submitButton("Login",array("class" => "login button")); ?>
		</div>
		
		<?php
		$this->endWidget();
		?>
	</div>
</article>
