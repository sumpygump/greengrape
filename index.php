<?php
/**
 * Greengrape index
 */

require 'init.php';

$config = array(
    'theme' => 'fulcrum',
);

$kernel = new Greengrape\Kernel($config);
Greengrape\Exception\Handler::initHandlers($kernel);

$kernel->execute();
