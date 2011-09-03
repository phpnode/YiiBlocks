<?php
/**
 * A single column view that will be rendered within the main admin layout
 * @uses string $content The content to render within this view
 */
?>
<?php $this->beginContent('packages.admin.views.layouts.main'); ?>
<article>
	<?php echo $content; ?>
</article>
<?php $this->endContent(); ?>