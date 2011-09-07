<?php
/**
 * The input form for the {@link ABlogPost} model
 * @uses BlogPost $model The ABlogPost model
 */
?>


<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'blog-post-form',
	'enableAjaxValidation'=>true,
)); ?>

	<div class='grid_12 alpha form'>
		<div class="row">
			<?php echo $form->labelEx($model,'title'); ?>
			<?php echo $form->textField($model,'title',array('size'=>50)); ?>
			<p class='hint'>The title of this page.</p>
			<?php echo $form->error($model,'title'); ?>
		</div>
		<div class='row'>
			<?php
			echo $form->labelEx($model,"content");
			$this->widget("packages.ckeditor.ACKEditor",array(
				"model" => $model,
				"attribute" => "content",
				"options" => array(
				),
			));
		?>
		</div>
	</div>
	<div class='grid_6 alpha form'>
			<div class="row">
				<?php echo CHtml::ajaxLink("Describe",
						array("/admin/blog/post/summarize", "limit" => 250),
						array(
							"data" => "js:$('#post-form').serialize()",
							"type" => "POST",
							"success" => "function(res) { if (res.length === 0) { return; } $('#ABlogPost_description').val(res); $('#describe').addClass('disabled').addClass('hidden'); }"
						),
						array(
							"id" => "describe",
							"class" => "right describe icon".($model->description == "" ? "" : " hidden disabled")
						)
					);
				?>
				<?php echo $form->labelEx($model,'description'); ?>
				<?php echo $form->textArea($model,'description',array('cols'=>50, 'rows' => 5)); ?>

				<p class='hint'>A short but meaningful description for this page, this will appear in the description meta tag.</p>
				<?php echo $form->error($model,'description'); ?>
			</div>
			<div class="row">

				<?php echo $form->labelEx($model,'tags'); ?>

				<?php
				$this->widget("packages.tags.components.ATagInputWidget",
							  array(
									"model" => $model,
									"attribute" => "tags",
								  	"options" => array(
										  "width" => "98%",
										  "autocomplete_url" => array("post/tag"),
									  )
							));
				?>
				<p class='hint'>Please add some relevant tags for this post.</p>
				<?php echo $form->error($model,'tags'); ?>
			</div>
			<div class="row">
				<?php echo $form->labelEx($model,'status'); ?>
				<?php echo $form->dropDownList($model,'status', array('draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived')); ?>
				<p class='hint'>Select the status for this Post.</p>
				<?php echo $form->error($model,'status'); ?>
			</div>
	</div>
	<div class='grid_6 omega form'>
			<div class="row">
				<?php echo CHtml::ajaxLink("Summarize",
						array("/admin/blog/post/summarize", "limit" => 1000),
						array(
							"data" => "js:$('#post-form').serialize()",
							"type" => "POST",
							"success" => "function(res) { if (res.length === 0) { return; }  $('#ABlogPost_summary').val(res); $('#summarize').addClass('disabled').addClass('hidden'); }"
						),
						array(
							"id" => "summarize",
							"class" => "right summarize icon".($model->summary == "" ? "" : " hidden disabled")
						)
					);
				?>
				<?php echo $form->labelEx($model,'summary'); ?>

				<?php
				echo $form->textArea($model,'summary',array('style' => 'height:375px;','cols'=>50, 'rows' => 17));
				?>
				<p class='hint'>Please summarize the post, this will appear as an introduction in the list of blog posts.</p>

				<?php echo $form->error($model,'summary'); ?>

			</div>

			<div class='row buttons'>
				<?php echo CHtml::submitButton('Save Post',array("class" => "save button")); ?>
				<?php
				if (!$model->isNewRecord) {
					echo CHtml::submitButton('Delete',array("confirm" => "Are you sure you want to delete this page? This cannot be undone!", "name" => "delete", "class" => "delete button"));
				}
				?>
			</div>
	</div>


<?php
$this->endWidget();
$options = array(
	"isNewRecord" => $model->isNewRecord,
);
$options = CJavaScript::encode($options);
$script = <<<JS
(function () {
	var opts = {$options};
	$("#ABlogPost_title").bind("keyup", function(e) {
		$("header h2.pageTitle").text((opts.isNewRecord ? "New Post: " : "Update Post: ") + $(this).val());
	});
	$("#ABlogPost_description").bind("keyup", function(e) {
		if ($("#ABlogPost_description").val().length === 0) {
			$("#describe").removeClass("disabled").removeClass("hidden");

		}
		else {
			$("#describe").addClass("disabled").addClass("hidden");
		}
	});
	$("#describe").bind("click", function (e) {
		if ($("#describe").hasClass("disabled")) {
			return false;
		}
	});
	$("#ABlogPost_summary").bind("keyup", function(e) {
		if ($("#ABlogPost_summary").val().length === 0) {
			$("#summarize").removeClass("disabled").removeClass("hidden");;

		}
		else {
			$("#summarize").addClass("disabled").addClass("hidden");

		}
	});
	$("#summarize").bind("click", function (e) {
		if ($("#summarize").hasClass("disabled")) {
			return false;
		}
	});
}());
JS;
Yii::app()->clientScript->registerScript("titleUpdater",$script);