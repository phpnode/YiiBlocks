<?php
/**
 * The administration view for the {@link BlogPost} model
 * @uses BlogPost $model The BlogPost model used for searching
 */
$this->breadcrumbs=array(
	'Blog Posts'=>array('index'),
	'Manage',
);


Yii::app()->clientScript->registerScript('search', "

$('#blog-post-grid input[type=\"checkbox\"]').live('change', function(e) {
	var checked;
	if ($(this).attr('id') === 'Post_all') {
		checked = $(this).is(':checked');
	}
	else {
		checked = $.fn.yiiGridView.getChecked('blog-post-grid','Post').length > 0;
	}
	if (checked && $('.bulkactions').not(':visible')) {
		$('.bulkactions').fadeIn(500);
	}
	else if (!checked && $('.bulkactions').is(':visible')) {
		$('.bulkactions').fadeOut(500);
	}
});
");
$this->beginWidget("AAdminPortlet",array(
									  "menuItems" => array(
										  array(
												"label" => "Create",
												"url" => array("/admin/blog/post/create"),
											),
									),
									  "title" => "Blog Posts"
								   ));
?>


<?php
echo CHtml::form(array("/admin/blog/post/bulk"));
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'blog-post-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'class' => 'CCheckBoxColumn',
			'selectableRows' => 2,
			'id' => 'Post',
		),
		array(
			'name' => 'title',
			'value' => 'CHtml::link(CHtml::encode($data->title),array("/admin/blog/post/update","id" => $data->id),array("class" => "icon edit"))',
			'type' => 'raw',
		),
		array(
			'name' => 'status',
			'value' => 'ucwords($data->status)',
			'filter' => array("draft" => "Draft", "published" => "Published", "archived" => "Archived"),
		),
		'timeAdded:datetime',

	),
));
?>
<div class='form bulkactions' style='display:none;'>
	<p>With Selected:</p>
	<?php
		echo CHtml::submitButton("Publish",array("name" => "publish", "class" => "button"));
		echo "&nbsp;&nbsp;";
		echo CHtml::submitButton("Archive",array("name" => "archive", "class" => "button"));
		echo "&nbsp;&nbsp;";
		echo CHtml::submitButton("Delete",array("name" => "delete", "class" => "delete button", "style" => "float:none;", "confirm" => "Are you sure you want to delete these posts?"));
		echo "&nbsp;&nbsp;";
	?>
</div>
<?php
echo CHtml::endForm();

$this->endWidget();
