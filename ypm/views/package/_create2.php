<?php
/**
 * A form for creating new packages
 * @uses APackage $model The package to create
 */
?>

<h1>Create A New Package <span class='right'>Step 2</span></h1>
<p>
	Fill out the form to begin creating a package that can be published with Yii Package Manager.	
</p>
<div class='form wide'>
	<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'package-form',
		'enableAjaxValidation'=>true,
		'stateful' => true,
	));
	echo CHtml::hiddenField("stage",2);
	echo $model->name;
	?>
	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<div class="row">
		<?php echo $form->labelEx($model,'version'); ?>
		<?php echo $form->textField($model,'version',array('size'=>60,'maxlength'=>250)); ?>
		<p class='hint'>Please enter a unique name for this package. Don't include any version or branch info in the name, you can add that later.</p>
		<?php echo $form->error($model,'version'); ?>
	</div>

	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Next',array("class" => "save button")); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>
