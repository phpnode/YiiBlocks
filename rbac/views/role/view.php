<?php
/**
 * Displays information for a particular {@link AAuthRole} model
 * @var AAuthRole $model The AAuthRole model to show
 */
$this->breadcrumbs=array(
	'Role Based Access Control' => array('rbac/index'),
	'Roles'=>array('index'),
	$model->name,
);

Yii::app()->clientScript->registerCoreScript("jquery-ui");
$this->beginWidget("AAdminPortlet",
				   array(

					  "title" => "Authorisation Role: ".$model->name,
					  "menuItems" => array(
						  array(
								"label" => "Edit",
								"url" => array("/admin/rbac/role/update", "slug" => $model->slug),
							),
							array(
								"label" => "Delete",
								"url" => "#",
								'linkOptions'=>array(
									'class' => 'delete',
									'submit'=>array('delete','slug'=>$model->slug),
									'confirm'=>'Are you sure you want to delete this item?'
								),
							)
					  )
				   ));
?>
<div class='grid_6 alpha'>

<?php

$this->widget("zii.widgets.CDetailView",
			  array(
				  "data" => $model,
				  "attributes" => array(
					  "name",
					  "description",
					  array(
						  "name" => "bizrule",
						  "type" => "raw",
						  "value" => $model->formatBizRule(),
					  )
				  )
			  ));
?>
</div>
<?php
$this->beginWidget("AAdminPortlet",
				   array(
						"title" => "Access Summary",
						"htmlOptions" => array("class" => "grid_6 omega"),
					   ));
echo "<ul>";
echo $model->summary;
echo "</ul>";
$this->endWidget();
?>
<div class='clear'></div>
<br /><br />
<?php
$this->beginWidget("AAdminPortlet",
				   array(
						"title" => "Tasks",
						"htmlOptions" => array("class" => "grid_6 alpha"),
						"menuItems" => array(
							array(
								"label" => "New Task",
								"url" => array("task/create", "assignTo" => $model->slug),
							),


						)
				   ));
?>
	<p class='info box'>Select the tasks that belong to this role, drag the tasks between the lists to select them.</p>

	<div class='grid_6 alpha'>
	<h4>Selected Tasks</h4>
	<p>These tasks belong to this role.</p>
	<?php
	$csrfData = json_encode(array(Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken));
	$ajax = CHtml::ajax(array(
								"url" => array("setTasks","slug" => $model->slug),
								"type" => "POST",
								"data" => 'js:(function(){
									var data = '.$csrfData.';
									data.tasks = $("#selectedTasks").sortable("toArray");
									return data;
								}())',
								"success" => "function(res){
									$('#unselectedTasks li.ui-state-highlight').
										removeClass('ui-state-highlight').
										addClass('ui-state-default');
									$('#selectedTasks li.ui-state-default').
										removeClass('ui-state-default').
										addClass('ui-state-highlight');
								}"
						));
	$selectedTasks = array();
	foreach($model->getChildren(AAuthItem::AUTH_TASK) as $item) {
		$item = AAuthItem::model()->findByPk($item->name);
		$selectedTasks[$item->name] = $item->createLink(null,null,array("title" => $item->description));
	}
	$unselectedTasks = array();
	foreach(AAuthTask::model()->findAll() as $item) {
		if (isset($selectedTasks[$item->name])) {
			continue;
		}
		$unselectedTasks[$item->name] = $item->createLink(null,null,array("title" => $item->description));
	}

	$this->widget('zii.widgets.jui.CJuiSortable', array(
					'id' => "selectedTasks",
					'itemTemplate' => '<li id="{id}" class="ui-state-highlight"><span class="ui-icon ui-icon-arrowthick-2-e-w left"></span>&nbsp;&nbsp;{content}</li>',

					'items'=>$selectedTasks,
					// additional javascript options for the accordion plugin
					'options'=>array(
						'connectWith' => '#unselectedTasks',
						'update' => 'js:function(event,ui){ '.$ajax.' }',
						'delay' => 300,
					),
	));
	?>
	</div>
	<div class='grid_6 omega'>
		<h4>Unselected Tasks</h4>
		<p>These tasks do not belong to this role.</p>
		<?php
		$this->widget('zii.widgets.jui.CJuiSortable', array(
			'id' => "unselectedTasks",
			'itemTemplate' => '<li id="{id}" class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-e-w left"></span>&nbsp;&nbsp;{content}</li>',
			'items'=>$unselectedTasks,
			// additional javascript options for the accordion plugin
			'options'=>array(
					'connectWith' => '#selectedTasks',
					'delay' => 300,
			),
		));
		?>
	</div>
<?php
$this->endWidget();
$this->beginWidget("AAdminPortlet",
				   array(
						"title" => "Operations",
						"htmlOptions" => array("class" => "grid_6 omega"),
					   "menuItems" => array(
							array(
								"label" => "New Operation",
								"url" => array("operation/create", "assignTo" => $model->slug),
							),


						)
				   ));
?>

	<p class='info box'>Select the operations that belong to this role, drag the operations between the lists to select them.</p>

	<div class='grid_6 alpha'>
	<h4>Selected Operations</h4>
	<p>These operations belong to this role.<br />&nbsp;</p>
	<?php
	$csrfData = json_encode(array(Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken));
	$ajax = CHtml::ajax(array(
								"url" => array("setOperations","slug" => $model->slug),
								"type" => "POST",
								"data" => 'js:(function(){
									var data = '.$csrfData.';
									data.operations = $("#selectedOperations").sortable("toArray");
									return data;
								}())',
								"success" => "function(res){
									$('#unselectedOperations li.ui-state-highlight').
										removeClass('ui-state-highlight').
										addClass('ui-state-default');
									$('#selectedOperations li.ui-state-default').
										removeClass('ui-state-default').
										addClass('ui-state-highlight');
								}"
						));
	$selectedOperations = array();
	foreach($model->getChildren(AAuthItem::AUTH_OPERATION) as $item) {
		$item = AAuthItem::model()->findByPk($item->name);
		$selectedOperations[$item->name] = CHtml::link($item->name,array("operation/view","name" => $item->name),array("title" => $item->description));
	}
	$unselectedOperations = array();
	foreach(AAuthOperation::model()->findAll() as $item) {
		if (isset($selectedOperations[$item->name])) {
			continue;
		}
		$unselectedOperations[$item->name] = CHtml::link($item->name,array("operation/view","name" => $item->name),array("title" => $item->description));
	}

	$this->widget('zii.widgets.jui.CJuiSortable', array(
					'id' => "selectedOperations",
					'itemTemplate' => '<li id="{id}" class="ui-state-highlight"><span class="ui-icon ui-icon-arrowthick-2-e-w left"></span>&nbsp;&nbsp;{content}</li>',

					'items'=>$selectedOperations,
					// additional javascript options for the accordion plugin
					'options'=>array(
						'connectWith' => '#unselectedOperations',
						'update' => 'js:function(event,ui){ '.$ajax.' }',
						'delay' => 300,
					),
	));
	?>
	</div>
	<div class='grid_6 omega'>
		<h4>Unselected Operations</h4>
		<p>These operations do not belong to this role.</p>
		<?php
		$this->widget('zii.widgets.jui.CJuiSortable', array(
			'id' => "unselectedOperations",
			'itemTemplate' => '<li id="{id}" class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-e-w left"></span>&nbsp;&nbsp;{content}</li>',
			'items'=>$unselectedOperations,
			// additional javascript options for the accordion plugin
			'options'=>array(
					'connectWith' => '#selectedOperations',
					'delay' => 300,
			),
		));
		?>
	</div>
<?php
$this->endWidget();
?>
<?php
$this->endWidget();
?>

