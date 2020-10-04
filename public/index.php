<?php

require dirname(__DIR__) . '/vendor/autoload.php';


session_start();

$router = new Core\Router();

$router->addRouteToRoutingTable('', ['controller' => 'Home', 'action' => 'index']);
$router->addRouteToRoutingTable('login', ['controller' => 'Login', 'action' => 'new']);
$router->addRouteToRoutingTable('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->addRouteToRoutingTable('{controller}/{action}');

$router->dispatchRoute($_SERVER['QUERY_STRING']);
