<?php
/**
 * The flexible input form for {@link AElasticSearchDocument}
 * @var AElasticSearchDocument $document The elastic search document
 */
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'aelastic-search-document-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<?php echo $form->labelEx($document,'name'); ?>
		<?php echo $form->textField($document,'name',array('size'=>60,'maxlength'=>64)); ?>
	</div>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array("class" => "save button")); ?>
	</div>

<?php $this->endWidget(); ?>