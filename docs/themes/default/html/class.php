<?php
/**
 * Gets html documentation for a class.
 * @var $class AClassDoc the class to document
 */
?>
<article>
<h1><?php echo $class->name; ?></h1>
<p class='introduction'><?php echo $class->getIntroduction(); ?></p>
<hr />

<table class='class summary'>
	<tr>
		<th>Inheritance</th>
		<td><?php
		$inheritance = array("class ".$class->name);

		$parent = $class->parent;
		if ($parent === false && $class->extends != "") {
			$inheritance[] = $class->typeLink($class->extends);
		}
		else {
			while($parent !== false) {
				$inheritance[] = $class->typeLink($parent->name);
				if ($parent->parent === false && $parent->extends != "") {
					$inheritance[] = $class->typeLink($parent->extends);
				}

				$parent = $parent->parent;
			}
		}
		echo implode(" - ",$inheritance);
		?></td>
	</tr>
	<?php
	$subClasses = $class->descendants;
	if (count($subClasses)) {
		?>
		<tr>
			<th>Subclasses</th>
			<td><?php
			foreach($subClasses as $subClass) {
				echo $class->typeLink($subClass->name)." ";
			}
			?></td>

		</tr>
		<?php
	}
	?>
	<?php
	if (count($class->authors)) {
		foreach($class->authors as $author) {
			echo "<tr>";
			echo "<th>Author</th>";
			echo "<td>".$author."</td>";
			echo "</tr>";
		}
	}
	if ($class->since != "") {
		echo "<tr>";
		echo "<th>Since</th>";
		echo "<td>".$class->since."</td>";
		echo "</tr>";
	}
	if ($class->version != "") {
		echo "<tr>";
		echo "<th>Version</th>";
		echo "<td>".$class->version."</td>";
		echo "</tr>";
	}
	if ($class->package != "") {
		echo "<tr>";
		echo "<th>Package</th>";
		echo "<td>".$class->package."</td>";
		echo "</tr>";
	}
	if ($class->filename) {
		echo "<tr>";
		echo "<th>Source Code</th>";
		echo "<td><strong>".$class->filename."</strong></td>";
		echo "</tr>";
	}
	?>
</table>
<?php
echo $class->getDescription();
?>
<hr />
<?php
$publicProperties = $class->allMembers("properties","isPublic");
if (count($publicProperties)) {
	echo "<h2>Public Properties</h2>";
	echo "<table class='public property summary'>";
	echo "<tr>";
	echo "<th>Name</th>";
	echo "<th>Type</th>";
	echo "<th>Introduction</th>";
	echo "<th>Defined In</th>";
	echo "</tr>";
	foreach($publicProperties as $property) {
		echo $this->renderFile("property-summary",array("property" => $property));
	}
	echo "</table>";
}
$protectedProperties = $class->allMembers("properties","isProtected");
if (count($protectedProperties)) {
	echo "<h2>Protected Properties</h2>";
	echo "<table class='protected property summary'>";
	echo "<tr>";
	echo "<th>Name</th>";
	echo "<th>Type</th>";
	echo "<th>Introduction</th>";
	echo "<th>Defined In</th>";
	echo "</tr>";
	foreach($protectedProperties as $property) {
		echo $this->renderFile("property-summary",array("property" => $property));
	}
	echo "</table>";
}

$privateProperties = $class->allMembers("properties","isPrivate");
if (count($privateProperties)) {
	echo "<h2>Private Properties</h2>";
	echo "<table class='private property summary'>";
	echo "<tr>";
	echo "<th>Name</th>";
	echo "<th>Type</th>";
	echo "<th>Introduction</th>";
	echo "<th>Defined In</th>";
	echo "</tr>";
	foreach($privateProperties as $property) {
		echo $this->renderFile("property-summary",array("property" => $property));
	}
	echo "</table>";
}


$publicMethods = $class->allMembers("methods","isPublic");
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

$protectedMethods = $class->allMembers("methods","isProtected");
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

$privateMethods = $class->allMembers("methods","isPrivate");
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
if (count($class->properties)) {
	foreach($class->properties as $property) {
		echo $this->renderFile("property-details",array("property" => $property));
	}
}
if (count($class->methods)) {
	foreach($class->methods as $method) {
		echo $this->renderFile("method-details",array("method" => $method));
	}
}
?>
</article>
