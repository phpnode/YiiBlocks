<?php
/**
 * The user registration form
 * @var AUser $model the user model
 */
$this->pageTitle = "Signup Now";
?>
<article>
	<h1>Signup Now</h1>
	<p>Please fill out the form below to signup now.</p>
	<div class='form'>
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'user-form',
			'enableAjaxValidation'=>true,
		)); ?>
		<div class="row">
			<?php echo $form->labelEx($model,'name'); ?>
			<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>450)); ?>
			<p class='hint'>Please enter your name.</p>
			<?php echo $form->error($model,'name'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>450)); ?>
			<p class='hint'>Please enter your email address.</p>
			<?php echo $form->error($model,'email'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>450)); ?>
			<p class='hint'>Please enter a memorable password.</p>
			<?php echo $form->error($model,'password'); ?>
		</div>
		<div class="row buttons">
			<?php echo CHtml::submitButton("Signup",array("class" => "signup button")); ?>
		</div>

		<?php
		$this->endWidget();
		?>
	</div>
</article>
