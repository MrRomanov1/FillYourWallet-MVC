<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Validation;

class Balance extends Authenticated {

    protected function before() {
        parent::before();

        $this->user = Auth::getUser();

    }

    public function currentMonthBalanceAction() {
        $date = static::getCurrentMonthDate();
        $balance = static::getBalanceData($date, $this->user->userId );
        $balance['beginDate'] = $date['beginDate'];
        $balance['endDate'] = $date['endDate'];
        $balance['period'] = 'z bieżącego miesiąca';
        $this->viewPage($balance);
    }

    public function currentYearBalanceAction() {
        $date = static::getCurrentYearDate();
        $balance = static::getBalanceData($date, $this->user->userId );
        $balance['beginDate'] = $date['beginDate'];
        $balance['endDate'] = $date['endDate'];
        $balance['period'] = 'z bieżącego roku';
        $this->viewPage($balance);
    }

    public function lastMonthBalanceAction() {
        $date = static::getLastMonthDate();
        $balance = static::getBalanceData($date, $this->user->userId );
        $balance['beginDate'] = $date['beginDate'];
        $balance['endDate'] = $date['endDate'];
        $balance['period'] = 'z poprzedniego miesiąca';
        $this->viewPage($balance);
    }

    public function customBalanceAction() {
        $date = static::getCustomDate();
        $balance = static::getBalanceData($date, $this->user->userId );
        $balance['beginDate'] = $date['beginDate'];
        $balance['endDate'] = $date['endDate'];
        $balance['period'] = 'od '.$date['beginDate'].' do '.$date['endDate'];
        $this->viewPage($balance);
    }

    public function viewPage($balance) {
        
        View::renderTemplate( 'Main/balance.html', [
            'user' => $this->user,
            'balance'=> $balance
        ] );
    }

    public function getBalanceData( $date, $userId ) {

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

        if ( $balance['balance'] > 0 ) {
            $balance['summaryId'] = 'balanceSummaryPositive';
            $balance['summaryText'] = 'Gratulacje! Świetnie zarządzasz finansami!';
        } else if ( $balance['balance'] < 0 ) {
            $balance['summaryId'] = 'balanceSummaryNegative';
            $balance['summaryText'] = 'Uważaj! Popadasz w długi!';
        } else {
            $balance['summaryId'] = 'balanceSummaryPositive';
            $balance['summaryText'] = 'Bilans zerowy. Czy masz wprowadzone dane?';
        }
        return $balance;
    }

    protected static function getCurrentMonthDate() {
        $beginDate = date( 'Y-m-d', strtotime( 'first day of this month' ) );
        $endDate = date( 'Y-m-d', strtotime( 'last day of this month' ) );
        $date = ['beginDate' => $beginDate,
        'endDate' => $endDate];

        return $date;
    }

    protected static function getLastMonthDate() {
        $beginDate = date( 'Y-m-d', strtotime( 'first day of previous month' ) );
        $endDate = date( 'Y-m-d', strtotime( 'last day of previous month' ) );
        $date = ['beginDate' => $beginDate,
        'endDate' => $endDate];

        return $date;
    }

    protected static function getCurrentYearDate() {
        $beginDate = date( 'Y-m-d', strtotime( 'first day of January' ) );
        $endDate = date( 'Y-m-d', strtotime( 'last day of December' ) );
        $date = ['beginDate' => $beginDate,
        'endDate' => $endDate];

        return $date;
    }

    protected static function getCustomDate() {
        if ( Validation::validateDateOrder( $_POST['beginDate'], $_POST['endDate'] ) && !Validation::validateDate( $_POST['endDate'] ) && !Validation::validateDate( $_POST['endDate'] ) ) {
            $date = ['beginDate' => $_POST['beginDate'],
            'endDate' => $_POST['endDate']];

            return $date;
        } else {
            return false;
        }
    }

    protected static function getSingleExpensesAction() {
        $date = ['beginDate' => $_GET['balanceBeginDate'],
        'endDate' => $_GET['balanceEndDate']];

        $categoryId = Expense::getUserExpenseCategoryId($_SESSION['userId'], $_GET['categoryName']);
        $singleExpenses = Expense::getSingleCategoryExpenses( $date, $_SESSION['userId'], $categoryId);

        header('Content-type: application/json');
        echo json_encode($singleExpenses);
    }

    protected static function getSingleExpenseDataAction() {
        $expenseId = $_GET['expenseId'];

        $singleExpenseData = Expense::getSingleExpenseData($expenseId);

        header('Content-type: application/json');
        echo json_encode($singleExpenseData);
    }
}
