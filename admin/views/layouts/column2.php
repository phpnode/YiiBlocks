<?php
/**
 * A 2 column view that will be rendered within the main admin layout
 * @uses string $content The content to render within this view
 */
?>
<?php $this->beginContent('packages.admin.views.layouts.main'); ?>
<section class='width_2'>
	<?php echo $content; ?>
</section>
<article class='width_1'>
	<header><h2>Operations</h2></header>
	<?php
			$this->beginWidget('zii.widgets.CPortlet', array(
				'title'=>'',
				'htmlOptions' => array(),
			));
			$this->widget('zii.widgets.CMenu', array(
				'items'=>$this->menu,
				'htmlOptions'=>array('class'=>'operations'),
			));
			$this->endWidget();
		?>
</article>
<?php $this->endContent(); ?>