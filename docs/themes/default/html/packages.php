<?php
/**
 * Shows a list of packages in the namespace
 * @uses ANamespaceDoc $namespace the global namespace
 */
?>
<table class='package summary'>
	<tr>
		<th>Name</th>
	</tr>
<?php
foreach($namespace->packages as $package) {
	echo "<tr>";
	echo "<td>".$package->name."</td>";
	echo "</tr>";
}
?>
</table>
