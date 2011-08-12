<?php
/**
 * Gets html documentation for an interface.
 * @uses $interface AInterfaceDoc the interface to document
 */
?>
<article>
<h1><?php echo $interface->name; ?></h1>
<p class='introduction'><?php echo $interface->introduction; ?></p>
<hr />

<table class='interface summary'>
	<tr>
		<th>Inheritance</th>
		<td><?php
		$inheritance = array("interface ".$interface->name); 
		
		$parent = $interface->parent;
		if ($parent === false && $interface->extends != "") {
			$inheritance[] = $interface->typeLink($interface->extends);
		}
		else {
			while($parent !== false) {
				$inheritance[] = $interface->typeLink($parent->name);
				if ($parent->parent === false && $parent->extends != "") {
					$inheritance[] = $interface->typeLink($parent->extends);
				}
				
				$parent = $parent->parent;
			}
		}
		echo implode(" - ",$inheritance);
		?></td>
	</tr>
	<?php
	$subClasses = $interface->descendants;
	if (count($subClasses)) {
		?>
		<tr>
			<th>Sub interfaces</th>
			<td><?php
			foreach($subClasses as $subClass) {
				echo $interface->typeLink($subClass->name)." ";
			}
			?></td>
			
		</tr>
		<?php
	}
	?>
	<?php
	if (count($interface->authors)) {
		foreach($interface->authors as $author) {
			echo "<tr>";
			echo "<th>Author</th>";
			echo "<td>".$author."</td>";
			echo "</tr>";
		}
	}
	if ($interface->since != "") {
		echo "<tr>";
		echo "<th>Since</th>";
		echo "<td>".$interface->since."</td>";
		echo "</tr>";
	}
	if ($interface->version != "") {
		echo "<tr>";
		echo "<th>Version</th>";
		echo "<td>".$interface->version."</td>";
		echo "</tr>";
	}
	if ($interface->package != "") {
		echo "<tr>";
		echo "<th>Package</th>";
		echo "<td>".$interface->package."</td>";
		echo "</tr>";
	}
	if ($interface->filename) {
		echo "<tr>";
		echo "<th>Source Code</th>";
		echo "<td><strong>".$interface->filename."</strong></td>";
		echo "</tr>";
	}
	?>
</table>
<?php
echo $interface->description;
?>
<hr />
<?php

$publicMethods = $interface->allMembers("methods","isPublic");
if (count($publicMethods)) {
	echo "<h2>Public Methods</h2>";
	echo "<table class='public method summary'>";
	echo "<tr>";
	echo "<th>Name</th>";
	echo "<th>Returns</th>";
	echo "<th>Introduction</th>";
	echo "<th>Defined In</th>";
	echo "</tr>";
	foreach($publicMethods as $method) {
		echo $this->renderFile("method-summary",array("method" => $method));
	}
	echo "</table>";
}

$protectedMethods = $interface->allMembers("methods","isProtected");
if (count($protectedMethods)) {
	echo "<h2>Protected Methods</h2>";
	echo "<table class='protected method summary'>";
	echo "<tr>";
	echo "<th>Name</th>";
	echo "<th>Returns</th>";
	echo "<th>Introduction</th>";
	echo "<th>Defined In</th>";
	echo "</tr>";
	foreach($protectedMethods as $method) {
		echo $this->renderFile("method-summary",array("method" => $method));
	}
	echo "</table>";
}

$privateMethods = $interface->allMembers("methods","isPrivate");
if (count($privateMethods)) {
	echo "<h2>Private Methods</h2>";
	echo "<table class='private method summary'>";
	echo "<tr>";
	echo "<th>Name</th>";
	echo "<th>Returns</th>";
	echo "<th>Introduction</th>";
	echo "<th>Defined In</th>";
	echo "</tr>";
	foreach($privateMethods as $method) {
		echo $this->renderFile("method-summary",array("method" => $method));
	}
	echo "</table>";
}

if (count($interface->methods)) {
	foreach($interface->methods as $method) {
		echo $this->renderFile("method-details",array("method" => $method));
	}
}
?>
</article>
