<?php
/**
 * Shows a list of items in the global namespace
 * @var ANamespaceDoc $namespace the global namespace
 */
?>
<article>
<table class='package summary'>
	<tr>
		<th>Package</th>
		<th>Name</th>
		<th>Description</th>
	</tr>
<?php

foreach($namespace->packages as $package) {
	$packageContents = array();
	foreach($package->interfaces as $item) {
		$packageContents[$item->name] = $this->renderFile("class-summary",array("class" => $item));
	}
	foreach($package->classes as $item) {
		$packageContents[$item->name] = $this->renderFile("class-summary",array("class" => $item));
	}
	foreach($package->functions as $item) {
		$packageContents[$item->name] = $this->renderFile("function-summary",array("function" => $item));
	}
	ksort($packageContents);
	$itemCount = count($packageContents);
	if ($itemCount == 0) {
		continue;
	}
	$first = array_shift($packageContents);
	echo "<tr>";
	echo "<td rowspan='$itemCount'>".$package->name."</td>";
	echo substr($first,strpos($first,">") + 1);
	echo implode("\n",$packageContents);

}

?>
</table>
</article>