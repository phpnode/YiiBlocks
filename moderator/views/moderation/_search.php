<?php
/**
 * The search form for the {@link AModerationItem} model
 * @uses AModerationItem $model The moderation item model
 */
?>
<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>


	<div class="row">
		<?php echo $form->label($model,'ownerModel'); ?>
		<?php echo $form->dropDownList($model,'ownerModel',AModerationItem::listOwnerModels(),array("empty" => "")); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model,'status', AModerationItem::$statuses,array("empty" => "")); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->