<?php
/**
 * Includes the common solr functionality
 */

Yii::import("packages.solr.*");

defined("SOLR_HOSTNAME") or define("SOLR_HOSTNAME","localhost");
defined("SOLR_PORT") or define("SOLR_PORT",8983);
defined("SOLR_PATH") or define("SOLR_PATH",null);