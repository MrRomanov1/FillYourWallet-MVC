<?php

require dirname(__DIR__) . '/vendor/autoload.php';


session_start();

error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

$router = new Core\Router();

$router->addRouteToRoutingTable('', ['controller' => 'Home', 'action' => 'index']);
$router->addRouteToRoutingTable('signup', ['controller' => 'Signup', 'action' => 'new']);
$router->addRouteToRoutingTable('main', ['controller' => 'Main', 'action' => 'index']);
$router->addRouteToRoutingTable('logout', ['controller' => 'Home', 'action' => 'destroy']);
$router->addRouteToRoutingTable('{controller}/{action}');

$router->dispatchRoute($_SERVER['QUERY_STRING']);
