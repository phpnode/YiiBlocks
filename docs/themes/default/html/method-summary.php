<?php
/**
 * Shows a summary of this method
 * @uses AClassMethodDoc $method The method to summarize
 */
?>
<tr>
	<td><?php echo $method->class->typeLink($method->name);	?>()</td>
	<td><?php echo (is_object($method->return) ? $this->typeLink($method->return->type) : "void"); ?></td>
	<td><?php echo $method->introduction; ?></td>
	<td><?php echo $method->class->typeLink($method->class->name); ?></td>
</tr>
