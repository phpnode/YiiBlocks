<?php
/**
 * Shows a summary of this property
 * @var AClassProperty $property The property to summarize
 */
?>
<tr>
	<td><?php echo $property->class->typeLink($property->name);	?></td>
	<td><?php echo $property->class->typeLink($property->type); ?></td>
	<td><?php echo $property->introduction; ?></td>
	<td><?php echo $property->class->typeLink($property->class->name); ?></td>
</tr>
