<?php
/**
 * Provides administration functionality for elastic search
 * @author Charles Pick
 * @package packages.elasticSearch.admin
 */
class AElasticSearchAdminModule extends ABaseAdminModule {
	/**
	 * The default controller, defaults to elasticSearch
	 * @var string
	 */
	public $defaultController = "elasticSearch";
	/**
	 * The menu items to show for this module.
	 * These menu items will be shown in the sidebar in the admin interface
	 * @see CMenu::$items
	 * @var array
	 */
	protected $_menuItems = array(
		array(
			"label" => "Elastic Search",
			"url" => array("/admin/elasticSearch/elasticSearch/index"),
			"linkOptions" => array(
				"class" => "elasticsearch icon",
			),
			"items" => array(
				array(
					"label" => "Indexes",
					"url" => array("/admin/elasticSearch/index/index"),
				)
			)
		)
	);
}