<?php

include_once 'src/bootstrap.php';
use Tecnotch\Factory as Factory;

define('BASE_DIR', __DIR__);
define('MODULE_DIR', BASE_DIR . '/modules');


//$config = Factory::config();
//$db = Factory::db();

$moduleGen = Factory::module();
$moduleGen->generate();