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
$router->addRouteToRoutingTable('get-single-expenses', ['controller' => 'Balance', 'action' => 'getSingleExpenses']);
$router->addRouteToRoutingTable('get-single-incomes', ['controller' => 'Balance', 'action' => 'getSingleIncomes']);
$router->addRouteToRoutingTable('get-single-expense-data', ['controller' => 'Balance', 'action' => 'getSingleExpenseData']);
$router->addRouteToRoutingTable('get-single-income-data', ['controller' => 'Balance', 'action' => 'getSingleIncomeData']);
$router->addRouteToRoutingTable('edit-single-expense', ['controller' => 'Balance', 'action' => 'editSingleExpense']);
$router->addRouteToRoutingTable('edit-single-income', ['controller' => 'Balance', 'action' => 'editSingleIncome']);
$router->addRouteToRoutingTable('config', ['controller' => 'ProfileManager', 'action' => 'config']);
$router->addRouteToRoutingTable('edit-income', ['controller' => 'ProfileManager', 'action' => 'editUserIncomeCategory']);
$router->addRouteToRoutingTable('edit-expense', ['controller' => 'ProfileManager', 'action' => 'editUserExpenseCategory']);
$router->addRouteToRoutingTable('edit-payment-method', ['controller' => 'ProfileManager', 'action' => 'editUserPaymentMethod']);
$router->addRouteToRoutingTable('add-income-category', ['controller' => 'ProfileManager', 'action' => 'addNewUserIncomeCategory']);
$router->addRouteToRoutingTable('add-expense-category', ['controller' => 'ProfileManager', 'action' => 'addNewUserExpenseCategory']);
$router->addRouteToRoutingTable('add-payment-method', ['controller' => 'ProfileManager', 'action' => 'addNewUserPaymentMethod']);
$router->addRouteToRoutingTable('delete-income-category', ['controller' => 'ProfileManager', 'action' => 'deleteUserIncomeCategory']);
$router->addRouteToRoutingTable('delete-expense-category', ['controller' => 'ProfileManager', 'action' => 'deleteUserExpenseCategory']);
$router->addRouteToRoutingTable('delete-payment-method', ['controller' => 'ProfileManager', 'action' => 'deleteUserPaymentMethod']);
$router->addRouteToRoutingTable('validate-password', ['controller' => 'ProfileManager', 'action' => 'checkUserPassword']);
$router->addRouteToRoutingTable('change-user-data', ['controller' => 'ProfileManager', 'action' => 'changeUserData']);
$router->addRouteToRoutingTable('logout', ['controller' => 'Home', 'action' => 'destroy']);
$router->addRouteToRoutingTable('{controller}/{action}');

$router->dispatchRoute($_SERVER['QUERY_STRING']);
