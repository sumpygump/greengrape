<?php
/**
 * Greengrape index
 */

require 'init.php';

$config = array(
    'theme' => 'fulcrum',
);

$app = new Greengrape\Kernel($config);
$app->execute();
