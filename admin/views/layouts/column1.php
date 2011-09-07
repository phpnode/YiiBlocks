<?php
/**
 * A single column view that will be rendered within the main admin layout
 * @uses string $content The content to render within this view
 */
?>
<?php $this->beginContent('packages.admin.views.layouts.main'); ?>
<article class="grid_12 alpha">
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>CMap::mergeArray(array("Admin" => array("/admin/")),$this->breadcrumbs),
			'htmlOptions' => array("class" => "breadcrumbs"),
		)); ?>
	<?php endif?>
	<?php echo $content; ?>
</article>
<?php $this->endContent(); ?>