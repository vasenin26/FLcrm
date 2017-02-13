<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

define( 'BASE_PATH', dirname(__FILE__) );
define( 'CORE_PATH', BASE_PATH.'/core' );

require( CORE_PATH.'/controller.class.php');
require( CORE_PATH.'/route.class.php');

$route = new Route();
$route->routeController();

$route->write();

?>