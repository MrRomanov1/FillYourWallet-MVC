<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Expense;
use \App\Models\PaymentMethod;
use \App\Validation;

class ExpenseManager extends Authenticated {

    protected function before() {
        parent::before();

        $this->user = Auth::getUser();

    }

    public function viewPageAction( $arg1 = 0, $arg2 = 0 ) {
        $success = false;
        $currentDate = Validation::getCurrentDate();

        $userExpenseCategories = Expense::getUserExpenseCategories( $this->user->userId );
        $userPaymentMethods = PaymentMethod::getUserPaymentMethods( $this->user->userId );
        View::renderTemplate( 'Main/expense.html', [
            'user' => $this->user,
            'expenses' => $arg1,
            'errors' => $arg2,
            'success' => $success,
            'userExpenseCategories' => $userExpenseCategories,
            'userPaymentMethods' => $userPaymentMethods,
            'currentDate' => $currentDate
        ] );
    }

    public function addExpenseAction() {

        $expense = new Expense( $_POST );

        if ( $expense->saveUserExpense( $this->user->userId ) ) {
            $success = true;
            $userExpenseCategories = Expense::getUserExpenseCategories( $this->user->userId );
            $userPaymentMethods = PaymentMethod::getUserPaymentMethods( $this->user->userId );
            $currentDate = Validation::getCurrentDate();
            View::renderTemplate( 'Main/expense.html', [
                'user' => $this->user,
                'success' => $success,
                'userExpenseCategories' => $userExpenseCategories,
                'userPaymentMethods' => $userPaymentMethods,
                'currentDate' => $currentDate                
            ] );
        } else {
            $expenses['amount'] = $_POST['amount'];
            $expenses['date'] = $_POST['date'];

            if ( isset( $_POST['comment'] ) ) {
                $expenses['comment'] = $_POST['comment'];
            }
            $errors['dateError'] = $expense -> dateErrors;
            $errors['amountError'] = $expense -> amountErrors;

            $this -> viewPageAction( $expenses, $errors );
        }
    }
}
