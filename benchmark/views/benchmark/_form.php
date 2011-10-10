<?php
/**
 * The input form for the {@link ABenchmark} model
 * @var ABenchmark $model The ABenchmark model
 */
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'abenchmark-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'url'); ?>
		<?php echo $form->textField($model,'url',array('size'=>60,'maxlength'=>1024)); ?>
		<p class='hint'>You can enter a static URL here if the page is external to the Yii application.</p>
		<?php echo $form->error($model,'url'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'route'); ?>
		<?php echo $form->textField($model,'route',array('size'=>60,'maxlength'=>255)); ?>
		<p class='hint'>If this page belongs to the Yii application, enter the route to the controller here.</p>
		<?php echo $form->error($model,'route'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'params'); ?>
		<?php
			$this->widget("packages.arrayInput.AArrayInputWidget",
						array(
							"model" => $model,
							"attribute" => "params"
						));
		?>
		<p class='hint'>Here you can add parameters to pass to the route.</p>
		<?php echo $form->error($model,'params'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array("class" => "save button")); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->