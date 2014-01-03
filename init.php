<?php
/**
 * Greengrape init file
 *
 * @package Greengrape
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');

defined('APP_PATH') || define('APP_PATH', realpath(__DIR__));

$autoloader = require __DIR__ . '/vendor/autoload.php';
$autoloader->add('Greengrape', __DIR__ . '/lib');
