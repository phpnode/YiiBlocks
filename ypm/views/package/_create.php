<?php
/**
 * A form for creating new packages
 * @var APackage $model The package to create
 */
?>

<p class='info box'>
	Fill out the form to begin creating a package that can be published with Yii Package Manager.
</p>
<div class='form'>
	<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'package-form',
		'enableAjaxValidation'=>true,
		'stateful' => true,
	));
	echo CHtml::hiddenField("stage",1);
	?>


	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>250)); ?>
		<p class='hint'>Please enter a unique name for this package. Don't include any version or branch info in the name, you can add that later.</p>
		<?php echo $form->error($model,'name'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('cols'=>60,'rows'=>6)); ?>
		<p class='hint'>Please enter a short description for this package.</p>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Next',array("class" => "save button")); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>
