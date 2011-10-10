<?php
/**
 * A view used to create new {@link User} models
 * @var User $model The User model to be inserted
 */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Create',
);

$this->beginWidget("AAdminPortlet",
				   array(
						"title" => "Create a new User"
				   ));
?>
<p class='info box'>Add a new user to the system by filling out the form below.</p>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<?php
$this->endWidget();