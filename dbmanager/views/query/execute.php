<?php
/**
 * Displays an input form to allow administrators to perform arbitrary SQL queries
 * @var ASqlFormModel $model the form model
 * @var QueryController $this the query controller
 * @var CSqlDataProvider|false $dataProvider the data provider containing the results of the query, or false if there is no query yet
 */
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'asql-form-model-form',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="info box">Fields with <span class="required">*</span> are required.</p>


	<div class="row">
		<?php echo $form->labelEx($model,'sql'); ?>
		<?php echo $form->textArea($model,'sql'); ?>

		<?php echo $form->error($model,'sql'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Execute',array("class" => "button")); ?>
	</div>

<?php $this->endWidget(); ?>
</div>
<?php
if (!$dataProvider) {
	return;
}
$data = $dataProvider->getData();
$columns = array_keys(array_shift($data));
foreach($columns as $i => $column) {
	if ($dataProvider->keyField === null) {
		$dataProvider->keyField = $column;
	}
	$columns[$i] = array("name" => $column);
}
$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'asql-form-model-grid',
		'dataProvider'=>$dataProvider,
		'columns' => $columns,
		'ajaxUpdate' => false,
		'htmlOptions' => array(
			'style' => 'overflow-x:auto;',
		)
	));