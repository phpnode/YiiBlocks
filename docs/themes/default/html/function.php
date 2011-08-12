<?php
/**
 * Gets html documentation for a function.
 * @uses $function AFunctionDoc the function to document
 */
?>
<article>
<h1><?php echo $function->name; ?></h1>
<p class='introduction'><?php echo $function->introduction; ?></p>
<hr />

<table class='function summary'>
<tr>
	<td colspan="3"><?php echo $this->highlight($function->signature()); ?></td>
</tr>

<?php
foreach($function->parameters as $parameter) {
	echo $this->renderFile("parameter-summary",array("parameter" => $parameter));
}
if (is_object($function->return)) {
	echo $this->renderFile("return-summary",array("return" => $function->return));
}

if (count($function->authors)) {
	foreach($function->authors as $author) {
		echo "<tr>";
		echo "<th>Author</th>";
		echo "<td colspan='2'>".$author."</td>";
		echo "</tr>";
	}
}
if ($function->since != "") {
	echo "<tr>";
	echo "<th>Since</th>";
	echo "<td colspan='2'>".$function->since."</td>";
	echo "</tr>";
}
if ($function->version != "") {
	echo "<tr>";
	echo "<th>Version</th>";
	echo "<td colspan='2'>".$function->version."</td>";
	echo "</tr>";
}
if ($function->package != "") {
	echo "<tr>";
	echo "<th>Package</th>";
	echo "<td colspan='2'>".$function->package."</td>";
	echo "</tr>";
}

?>
<tr>
	<th>Source Code:</th>
	<td colspan="2"><?php
	echo "<strong>".$function->filename."</strong> ";
	echo CHtml::link("show","#",array("id" => $function->name."-source-link"));
	echo CHtml::tag("div",array("class" => "sourceCode", "id" => $function->name."-source"),$this->highlight($function->getSourceCode()));
	$script = <<<JS
$("#{$function->name}-source-link").bind("click",function(e){
	e.preventDefault();
	if ($("#{$function->name}-source").is(":visible")) {
		$("#{$function->name}-source").hide();
		$("#{$function->name}-source-link").html("show");
	}
	else {
		$("#{$function->name}-source").show();
		$("#{$function->name}-source-link").html("hide");
	}
});
JS;
	echo CHtml::script($script);
	
	
	?></td>
</tr>
	
</table>

<?php
echo $function->description;
?>

</article>