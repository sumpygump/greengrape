<?php
/**
 * Test bootstrap
 *
 * @package Apricot
 */

date_default_timezone_set('America/Chicago');

defined('APP_PATH') || define('APP_PATH', realpath(dirname(__DIR__)));

$autoload = require APP_PATH . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

require_once 'BaseTestCase.php';
