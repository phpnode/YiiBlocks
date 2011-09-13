<?php
/**
 * A 2 column view that will be rendered within the main admin layout
 * @uses string $content The content to render within this view
 */
?>
<?php $this->beginContent('packages.admin.views.layouts.main'); ?>
<section id='sidebar' class='grid_3 alpha'>
	<a href='#' class='collapsible'>Collapse</a>
	<header id='top'>
		<?php
			echo CHtml::link(Yii::app()->name." Admin",array("/admin/default/index"),array("id" => "logo"));
		?>
		<div id='userInfo'>
			<?php
			if (!Yii::app()->user->isGuest) {
				echo "Welcome, ".CHtml::encode(Yii::app()->user->name);
				echo "&nbsp;";
				echo CHtml::link("Logout",array("/site/logout"),array("class" => "logout button"));
			}
			?>
		</div>
	</header>

	<br />
		<?php
		$this->widget('zii.widgets.CMenu',array(
			"activateParents" => true,
			'items'=>Yii::app()->getModule("admin")->mainMenu
		));
		?>
</section>
<section id='content' class='grid_9 omega'>
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>CMap::mergeArray(array("Admin" => array("/admin/")),$this->breadcrumbs),
			'htmlOptions' => array("class" => "breadcrumbs"),
		)); ?>

	<?php endif?>
	<?php echo $content; ?>
</section>
<?php $this->endContent(); ?>