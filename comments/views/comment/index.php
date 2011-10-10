<?php
/**
 * The administration view for the {@link Comment} model
 * @var Comment $model The Comment model used for searching
 */
$this->breadcrumbs=array(
    'Comments'=>array('index'),
    'Manage',
);

$this->menu=array(
    array('label'=>'List Comment', 'url'=>array('index')),
    array('label'=>'Create Comment', 'url'=>array('create')),
);

?>

<h1>Manage Comments</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'comment-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
     	array(
            'class'=>'CCheckBoxColumn',
            'selectableRows' => 2,
        ),
        array(
			'name' => 'authorName',
			'value' => 'CHtml::link(CHtml::encode($data->authorName),"mailto:".$data->authorEmail,array()).
						"<br />".
						CHtml::link(CHtml::encode($data->authorUrl),$data->authorUrl)',
			'type' => 'raw',
			'header' => 'Author',
		),
		array(
			'name' => 'content',
			'value' => 'CHtml::link(Yii::app()->format->datetime($data->timeAdded),"#").
						"<br /><br /><p>".
						nl2br(CHtml::encode($data->content)).
						"</p>".
						"<div class=\'adminLinks\'>".
						($data->isApproved ?
							CHtml::link("Approved","#",array("class" => "icon approved"))
							:
							CHtml::link("Pending Approval","#",array("class" => "icon pending"))
						).
						" | ".
						($data->isSpam ?
							CHtml::link("Marked as Spam","#",array("class" => "icon spam"))
							:
							CHtml::link("Not Spam","#",array("class" => "icon notspam"))
						).
						"</div>"
						',
			'type' => 'raw',
			'header' => 'Comment'
		),
		array(
			'value' => '$data->owner->createLink()',
			'type' => 'raw',
			'header' => 'In Response To',
		)
    ),
)); ?>