<?php
/**
 * Shows a summary of this parameter
 * @uses AParameterDoc $parameter The parameter to summarize
 */
?>
<tr>
	<th>$<?php echo $parameter->name;	?></th>
	<td><?php
	if ($parameter->function instanceof AClassMethodDoc) {
		echo $parameter->function->class->typeLink($parameter->type);
	}
	else {
		echo $parameter->function->typeLink($parameter->type);
	}	
	?></td>
	<td><?php echo $parameter->description; ?></td>
</tr>
