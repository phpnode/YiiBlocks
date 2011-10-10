<?php
/**
 * An form for adding reviews by users.
 * @var AReview $model The review model
 * @var CActiveRecord $owner The model being reviewed
 */
?>

<h1><?php
	if ($owner instanceof IANameable) {
		echo "Review ".CHtml::encode($owner->getName());
	}
	else {
		echo "Add Review";
	}
?></h1>
<div class='form wide'>
	<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'review-form',
		'enableAjaxValidation'=>true,
	));
	?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>250)); ?>
		<p class='hint'>A brief title or summary for your review.</p>
		<?php echo $form->error($model,'title'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'content'); ?>
		<?php echo $form->textArea($model,'content',array('cols'=>60,'rows'=>6)); ?>
		<p class='hint'><?php
		if ($owner instanceof IANameable) {
			echo "Please tell us what you think about ".CHtml::encode($owner->getName()).".";
		}
		else {
			echo "Please tell us what you think.";
		}
		?></p>
		<?php echo $form->error($model,'content'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'score'); ?>
		<?php
			$this->widget("CStarRating",array(
				"model" => $model,
				"attribute" => "score"
				));
		?>
		<p class='hint'><?php
			if ($owner instanceof IANameable) {
			echo "How many stars would you give ".CHtml::encode($owner->getName())."?";
		}
		else {
			echo "How many stars would you give it?";
		}
		?></p>
		<?php echo $form->error($model,'score'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Add Review' : 'Save',array("class" => "save button")); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>
