<?php
/**
 * The input form for the {@link AComment} model
 * @var AComment $model The Comment model
 * @var array $action The comment action route
 */

?>
<div class="form">

<?php

$form=$this->beginWidget('CActiveForm', array(
    'id'=>'comment-form',
    'enableAjaxValidation'=>true,
    'action' => $action,

));

?>


    <?php echo $form->errorSummary($model); ?>
	<?php
	if (Yii::app()->user->isGuest) {
	?>
    <div class="row">
        <?php echo $form->labelEx($model,'authorName'); ?>
        <?php echo $form->textField($model,'authorName',array('size'=>50,'maxlength'=>50)); ?>
        <?php echo $form->error($model,'authorName'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'authorEmail'); ?>
        <?php echo $form->textField($model,'authorEmail',array('size'=>60,'maxlength'=>450)); ?>
        <?php echo $form->error($model,'authorEmail'); ?>
    </div>
    <?php
	}
    ?>

    <div class="row">
        <?php echo $form->labelEx($model,'content'); ?>
        <?php echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
        <?php echo $form->error($model,'content'); ?>
    </div>

	<?php
	if (Yii::app()->getModule("comments")->isCaptchaRequired) {
	?>
	<div class="row">
		<?php echo $form->labelEx($model,'verifyCode'); ?>
		<div>
		<?php $this->widget('CCaptcha',array("captchaAction" => Yii::app()->getModule("comments")->captchaAction)); ?>
		<br />
		<?php echo $form->textField($model,'verifyCode'); ?>

		<div class="hint">Please enter the letters as they are shown in the image above.
		<br/>Letters are not case-sensitive.</div>
		<?php echo $form->error($model,'verifyCode'); ?>
		</div>
	</div>
	<?php
	}
	?>
    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Post Comment' : 'Save Comment', array("class" => "save button")); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->