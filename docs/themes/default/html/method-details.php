<?php
/**
 * Shows the details for a method
 * @var AClassMethodDoc $method The property to show details for
 */
?>
<h3 id="<?php echo $method->name; ?>-method"><?php echo $method->name; ?>()</h3>
method <?php
if ($method->since !== null) {
	echo "(available since v".$method->since.")";
}
?><br />
<table class='method summary'>
<tr>
	<td colspan="3"><?php echo $this->highlight($method->signature()); ?></td>
</tr>

<?php
foreach($method->parameters as $parameter) {
	echo $this->renderFile("parameter-summary",array("parameter" => $parameter));
}
if (is_object($method->return)) {
	echo $this->renderFile("return-summary",array("return" => $method->return));
}
?>
<tr>
	<th>Source Code:</th>
	<td colspan="2"><?php
	echo "<strong>".$method->class->filename."</strong> ";
	echo CHtml::link("show","#",array("id" => $method->name."-source-link"));
	echo CHtml::tag("div",array("class" => "sourceCode", "id" => $method->name."-source"),$this->highlight($method->getSourceCode()));
	$script = <<<JS
$("#{$method->name}-source-link").bind("click",function(e){
	e.preventDefault();
	if ($("#{$method->name}-source").is(":visible")) {
		$("#{$method->name}-source").hide();
		$("#{$method->name}-source-link").html("show");
	}
	else {
		$("#{$method->name}-source").show();
		$("#{$method->name}-source-link").html("hide");
	}
});
JS;
	echo CHtml::script($script);


	?></td>
</tr>
</table>
<br />
<?php

echo "<br />";
echo $method->description;
$see = $method->see;
if (count($see)) {
	echo "<br /><strong>See Also:</strong><br />";
	echo "<ul>";
	foreach($see as $item) {
		echo "<li>";
		echo $method->class->typeLink($item);
		echo "</li>";
	}
	echo "</ul>";
}
?>
<br /><hr />


