<?php

require dirname(__DIR__) . '/vendor/autoload.php';


session_start();

error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

$router = new Core\Router();

$router->addRouteToRoutingTable('', ['controller' => 'Home', 'action' => 'index']);
$router->addRouteToRoutingTable('login', ['controller' => 'Home', 'action' => 'create']);
$router->addRouteToRoutingTable('signup', ['controller' => 'Signup', 'action' => 'new']);
$router->addRouteToRoutingTable('main', ['controller' => 'Main', 'action' => 'index']);
$router->addRouteToRoutingTable('income', ['controller' => 'IncomeManager', 'action' => 'viewPage']);
$router->addRouteToRoutingTable('addIncome', ['controller' => 'IncomeManager', 'action' => 'addIncome']);
$router->addRouteToRoutingTable('expense', ['controller' => 'ExpenseManager', 'action' => 'viewPage']);
$router->addRouteToRoutingTable('addExpense', ['controller' => 'ExpenseManager', 'action' => 'addExpense']);
$router->addRouteToRoutingTable('currentMonthBalance', ['controller' => 'Balance', 'action' => 'currentMonthBalance']);
$router->addRouteToRoutingTable('currentYearBalance', ['controller' => 'Balance', 'action' => 'currentYearBalance']);
$router->addRouteToRoutingTable('lastMonthBalance', ['controller' => 'Balance', 'action' => 'lastMonthBalance']);
$router->addRouteToRoutingTable('customBalance', ['controller' => 'Balance', 'action' => 'customBalance']);
$router->addRouteToRoutingTable('config', ['controller' => 'ProfileManager', 'action' => 'config']);
$router->addRouteToRoutingTable('logout', ['controller' => 'Home', 'action' => 'destroy']);
$router->addRouteToRoutingTable('{controller}/{action}');

$router->dispatchRoute($_SERVER['QUERY_STRING']);
