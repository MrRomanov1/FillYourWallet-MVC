<?php

require dirname(__DIR__) . '/vendor/autoload.php';


session_start();

error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

$router = new Core\Router();

$router->addRouteToRoutingTable('', ['controller' => 'Home', 'action' => 'index']);
$router->addRouteToRoutingTable('login', ['controller' => 'Login', 'action' => 'new']);
$router->addRouteToRoutingTable('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->addRouteToRoutingTable('{controller}/{action}');

$router->dispatchRoute($_SERVER['QUERY_STRING']);
