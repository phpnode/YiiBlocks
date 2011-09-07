<?php
/**
 * The AElasticSearchDocument search form
 * @var AElasticSearchDocumentType $type The document type to search in
 */
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route,array("name" => $type->index->name, "type" => $type->name)),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo CHtml::textField("q",(isset($_GET['q']) ? $_GET['q'] : ""),array("placeholder" => "Search")) ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search',array("class" => "search button")); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->