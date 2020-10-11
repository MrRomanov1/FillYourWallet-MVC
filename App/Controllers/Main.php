<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;

class Main extends Authenticated {

    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();        
        
    }
    
    public function indexAction()
    {
        $date = static::getCurrentMonthDate();
        $balance = static::getMainSiteBalanceData($date, $this->user->userId);
        
        View::renderTemplate('Main/index.html', [
            'user' => $this->user,
            'balance' => $balance
        ]);
    }
    
     protected static function getCurrentMonthDate() {
        $beginDate = date( 'Y-m-d', strtotime( 'first day of this month' ) );
        $endDate = date( 'Y-m-d', strtotime( 'last day of this month' ) );
        $date = ['beginDate' => $beginDate,
        'endDate' => $endDate];

        return $date;
    }
    
    public function getMainSiteBalanceData( $date, $userId ) {

        $balance['incomes'] = Income::getIncomes( $date, $userId );
        $balance['incomeTotal'] = Income::getIncomesTotal( $date, $userId );
        $balance['expenses'] = Expense::getExpenses( $date, $userId ); 
        $balance['expenseTotal'] = Expense::getExpensesTotal( $date, $userId );
        $balance['date'] = $date;

        $balance['incomeSum'] = 0;
        $balance['expenseSum'] = 0;

        foreach ( $balance['incomeTotal'] as $value ) {
            $balance['incomeSum'] += $value['ROUND(SUM(incomes.amount), 2)'];
        }

        foreach ( $balance['expenseTotal'] as $value ) {
            $balance['expenseSum'] += $value['ROUND(SUM(expenses.amount), 2)'];
        }

        $balance['balance'] = $balance['incomeSum'] - $balance['expenseSum'];

        return $balance;
    }
}
