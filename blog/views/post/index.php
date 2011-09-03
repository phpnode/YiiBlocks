<?php
/**
 * The index view for the {@link ABlogPost} model
 * @uses CActiveDataProvider $dataProvider The data provider for the models
 */
$this->breadcrumbs=array(
	'Blog Posts'
);
?>


<?php
$this->widget('zii.widgets.CListView', array(
	'id'=>'blog-post-list',
	'dataProvider'=>$dataProvider,
	'itemView' => '_view',
));
?>

