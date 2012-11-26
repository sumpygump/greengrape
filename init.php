<?php
/**
 * Greengrape init file
 *
 * @package Greengrape
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');

defined('APP_PATH') || define('APP_PATH', realpath(__DIR__));

require_once 'vendor/autoload.php';

// Greengrape classes
include_once 'lib/Greengrape/Kernel.php';
include_once 'lib/Greengrape/Config.php';
include_once 'lib/Greengrape/Request.php';
include_once 'lib/Greengrape/Sitemap.php';
include_once 'lib/Greengrape/Cache.php';
include_once 'lib/Greengrape/Location.php';
include_once 'lib/Greengrape/NavigationItem.php';
include_once 'lib/Greengrape/View.php';
include_once 'lib/Greengrape/View/Theme.php';
include_once 'lib/Greengrape/View/Template.php';
include_once 'lib/Greengrape/View/Layout.php';
include_once 'lib/Greengrape/View/Content.php';
include_once 'lib/Greengrape/View/AssetManager.php';
include_once 'lib/Greengrape/Exception/GreengrapeException.php';
include_once 'lib/Greengrape/Exception/Handler.php';
include_once 'lib/Greengrape/Exception/NotFoundException.php';
