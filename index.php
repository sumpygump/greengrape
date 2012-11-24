<?php
/**
 * Greengrape index
 */

require 'init.php';

$config = new Greengrape\Config('config.ini');

$kernel = new Greengrape\Kernel($config);
Greengrape\Exception\Handler::initHandlers($kernel);

$kernel->execute();
