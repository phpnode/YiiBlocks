<?php
/**
 * Shows links to the various role based access control actions
 */
$this->breadcrumbs=array(
	'Role Based Access Control'
);
$this->beginWidget("AAdminPortlet",array(
									"sidebarMenuItems" => array(
														array(
															"label" => "Roles",
															"url" => array("role/index"),
														),
														array(
															"label" => "Tasks",
															"url" => array("task/index"),
														),
														array(
															"label" => "Operations",
															"url" => array("operation/index"),
														),
													),
									  "title" => "Role Based Access Control"
								   ));
?>
<p>Role based access control is an access control system based on the concepts of roles, tasks and operations.</p>
		<p>Each user can have one or many roles, each role consists of one or many tasks and each task consists of one of many operations.</p>
		<p>Operations are the lowest level in the authorisation hierarchy, they represent particular actions that can be performed on the site, e.g. posting a blog post.</p>
		<?php
		echo CHtml::link("Roles",array("role/index"),array("class" => "button"))."&nbsp;&nbsp;";
		echo CHtml::link("Tasks",array("task/index"),array("class" => "button"))."&nbsp;&nbsp;";
		echo CHtml::link("Operations",array("operation/index"),array("class" => "button"))."&nbsp;&nbsp;";
		?>
<?php
$this->endWidget();
?>
<article>
	<header>
		<h1>Role Based Access Control</h1>
	</header>
	<section class='sidebar'>
		<?php
			$this->widget("zii.widgets.CMenu",array(
													"htmlOptions" => array(
														"class" => "menu",
													),
													"items" => array(
														array(
															"label" => "Roles",
															"url" => array("role/index"),
														),
														array(
															"label" => "Tasks",
															"url" => array("task/index"),
														),
														array(
															"label" => "Operations",
															"url" => array("operation/index"),
														),
													)
											  ));
	?>
	</section>
	<section class='content'>
		<p>Role based access control is an access control system based on the concepts of roles, tasks and operations.</p>
		<p>Each user can have one or many roles, each role consists of one or many tasks and each task consists of one of many operations.</p>
		<p>Operations are the lowest level in the authorisation hierarchy, they represent particular actions that can be performed on the site, e.g. posting a blog post.</p>
		<?php
		echo CHtml::link("Roles",array("role/index"),array("class" => "button"))."&nbsp;&nbsp;";
		echo CHtml::link("Tasks",array("task/index"),array("class" => "button"))."&nbsp;&nbsp;";
		echo CHtml::link("Operations",array("operation/index"),array("class" => "button"))."&nbsp;&nbsp;";
		echo str_repeat("<br />",20);

		?>
		testtes testsetst
	</section>
</article>