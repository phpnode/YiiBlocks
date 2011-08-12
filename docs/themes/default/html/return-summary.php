<?php
/**
 * Shows a summary of this return statement
 * @uses AReturnDoc $return The return statement to summarize
 */
?>
<tr>
	<th>{returns}</th>
	<td><?php 
	if ($return->function instanceof AClassMethodDoc) {
		echo $return->function->class->typeLink($return->type);
	}
	else {
		echo $return->function->typeLink($return->type);
	}
		
	?></td>
	<td><?php echo $return->description; ?></td>
</tr>
