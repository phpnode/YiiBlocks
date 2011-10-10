<?php
Yii::import("packages.admin.components.*");
Yii::import("packages.benchmark.components.*");
Yii::import("packages.benchmark.models.*");
/**
 * Provides a means for running benchmarks on the site and recording results
 * @author Charles Pick
 * @package packages.benchmark
 */
class ABenchmarkModule extends ABaseAdminModule {
	/**
	 * The menu items to show for this module.
	 * These menu items will be shown in the sidebar in the admin interface
	 * @see CMenu::$items
	 * @var array
	 */
	protected $_menuItems = array(
		array(
			"label" => "Benchmarks",
			"url" => array("/admin/benchmark/benchmark/index"),
			"linkOptions" => array(
				"class" => "benchmark icon",
			),
		)
	);
	/**
	 * The path to the ab executable.
	 * @var string
	 */
	public $abPath;

}