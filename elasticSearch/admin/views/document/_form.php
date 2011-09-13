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
<?php
	$this->widget("packages.arrayInput.AArrayInputWidget",array(
															 "name" => get_class($document),
															 "value" => $document->toArray(),
														  ));
	?>
	<br /><br />
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Save',array("class" => "save button")); ?>
	</div>
<?php $this->endWidget(); ?>