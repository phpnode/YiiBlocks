<?php
/**
 * A form for creating new packages
 * @var APackage $model The package to create
 */
?>

<h1>Edit Package: <?php echo CHtml::encode($model->name); ?></h1>
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
	<div class='row'>
	<?php
	echo $form->labelEx($model,'files');
	echo "<div class='fileBrowser'>";
	$this->widget("packages.fileManager.widgets.AFileBrowser",array(
		"model" => $model,
		"attribute" => "files",
		"multiple" => true,
		"basePath" => Yii::getPathOfAlias("packages.".$model->name),
	));
	echo "</div>";
	?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Next',array("class" => "save button")); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>
