<?php
/**
 * An interface for creating new packages
 * @uses APackage $model The package to create
 */
?>
<article class='width_3'>
	<?php
	$this->renderPartial("_create",array("model" => $model));
	?>
</article>
