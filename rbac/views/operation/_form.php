<?php
/**
 * The input form for the {@link AAuthOperation} model
 * @uses AAuthOperation $model The AAuthOperation model
 */
$lookupUrl = json_encode($this->createUrl("findRoute"));
$script = <<<JS
$("#AAuthOperation_name").bind("change",function(e){
	if ($(this).val().substr(0,1) === "/") {
		// this could be a route, let's look it up
		$.ajax({
			"url": {$lookupUrl},
			"data": $("#aauth-operation-form").serialize(),
			"type": "POST",
			"success": function(res) {
				$("#AAuthOperation_description").val(res.comment);
			}
		});
	}
});
JS;
Yii::app()->clientScript->registerScript("routeFinder",$script);
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'aauth-operation-form',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>


	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>64)); ?>
		<p class='hint'>Please enter a unique name for this operation. E.g. 'createBlogPost' or '/blog/post/create' to mark the operation as an URL route.</p>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
		<p class='hint'>Please enter a short description for this operation</p>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'bizrule'); ?>
		<?php echo $form->textArea($model,'bizrule',array('rows'=>6, 'cols'=>50)); ?>
		<p class='hint'>Here you can enter a <b>valid</b> PHP expression that determines whether this operation really applies.</p>
		<?php echo $form->error($model,'bizrule'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array("class" => "save button")); ?>
	</div>

<?php $this->endWidget(); ?>