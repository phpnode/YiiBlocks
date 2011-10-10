<?php
/**
 * A view used to create new {@link AUserGroup} models
 * @var AUserGroup $model The AUserGroup model to be inserted
 */

$this->breadcrumbs=array(
	'User Groups'=>array('index'),
	'Create',
);

$this->beginWidget("AAdminPortlet",
				   array(
						"title" => "Create a new User Group"
				   ));
?>
<p class='info box'>Fill out the form to create a new User Group. You'll be able to add members after this step.</p>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<?php
$this->endWidget();