<?php
/**
 * An interface for creating new packages
 * @uses APackage $model The package to create
 */
?>
<article class='width_2'>
	<?php
	$this->renderPartial("_create".$stage,array("model" => $model));
	?>
</article>
