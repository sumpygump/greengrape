<?php
/**
 * Greengrape init file
 *
 * @package Greengrape
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');

defined('APP_PATH') || define('APP_PATH', realpath(__DIR__));

// Autoloader
if (file_exists(__DIR__ . '/vendor.phar')) {
    $autoload = require_once __DIR__ . '/vendor.phar';
} elseif (file_exists(__DIR__ . '/vendor/autoload.php')) {
    $autoload = require_once __DIR__ . '/vendor/autoload.php';
} else {
    die('Autoloader not found. Run `composer install`');
}

$autoload->add('Greengrape', __DIR__ . '/lib');
