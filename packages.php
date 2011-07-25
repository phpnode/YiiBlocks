<?php
Yii::setPathOfAlias("packages", dirname(__FILE__));
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
			"moderator.interfaces",
			"moderator.components",
			"moderator.models",
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
	Yii::import("packages.".$component.".*");
}

