<?php
Yii::setPathOfAlias("blocks", dirname(__FILE__));
$components = array(
			"elasticSearch",
			"curl",
			"flashMessages",
			"redis",
			"actions",
			"decorating",
			"ownable",
			"nameable",
			"linkable",
			"services",
			"ratings.interfaces",
			"ratings.models",
			"ratings.components",
			"voting.interfaces",
			"voting.models",
			"voting.components",
			"reviews.interfaces",
			"reviews.models",
			"reviews.components"
			);
foreach($components as $component) {
	Yii::import("blocks.".$component.".*");
}

