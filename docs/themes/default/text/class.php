<?php
/**
 * Gets text documentation for a class.
 * @var $class AClassDoc the class to document
 */
echo str_repeat("-",50)."\n";
echo $class->name."\n";

echo "\n";
echo $class->description."\n\n";
if (count($class->constants)) {
	echo "Constants:\n";
	foreach($class->constants as $const) {
		echo "\t".$const->signature()."\n\n";
	}
	echo "\n";
}

if (count($class->properties)) {
	echo "Properties:\n";

	foreach($class->properties as $property) {
		echo "\t".$property->signature()."\n";
		echo "\t\t".$property->description."\n\n";
	}
	echo "\n";
}

if (count($class->methods)) {
	echo "Methods:\n";
	foreach($class->methods as $method) {
		echo "\t".$method->signature()."\n";
		echo "\t\t".$method->description."\n";
		if (count($method->parameters)) {
			echo "\t\tParameters:\n";
			foreach($method->parameters as $param) {
				echo "\t\t\t".$param->signature()." - ".$param->description."\n";
			}
		}
		if (is_object($method->return)) {
			echo "\t\tReturns:\n";
			echo "\t\t\t".$method->return->type." - ".$method->return->description."\n";
		}
		echo "\n";
	}
	echo "\n";
}
echo "\n\n";
