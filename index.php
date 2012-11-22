<?php
/**
 * Greengrape index
 */

require 'init.php';

$config = array(
    'theme' => 'fulcrum',
);

$app = new Greengrape\Kernel();
$app->execute();
