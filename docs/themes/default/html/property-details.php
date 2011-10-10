<?php
/**
 * Shows the details for a property
 * @var AClassPropertyDoc $property The property to show details for
 */
?>
<h3 id="<?php echo $property->name; ?>-property"><?php echo $property->name; ?></h3>
property <?php
if ($property->since !== null) {
	echo "(Available since v".$property->since.")";
}
?><br />
<table class="property summary">
	<tr>
		<td colspan="3"><?php echo $this->highlight($property->signature()); ?></td>
	</tr>
</table>
<br />
<?php
echo $property->description;

$see = $property->see;
if (count($see)) {
	echo "<br /><strong>See Also:</strong><br />";
	echo "<ul>";
	foreach($see as $item) {
		echo "<li>";
		echo $property->class->typeLink($item);
		echo "</li>";
	}
	echo "</ul>";
}
?>
<br /><hr />


