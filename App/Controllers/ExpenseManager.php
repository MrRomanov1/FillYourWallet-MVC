<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Expense;

class ExpenseManager extends Authenticated {

    protected function before() {
        parent::before();

        $this->user = Auth::getUser();

    }

    public function viewPageAction( $arg1 = 0, $arg2 = 0 ) {
        $success = false;
        View::renderTemplate( 'Main/expense.html', [
            'user' => $this->user,
            'expenses' => $arg1,
            'errors' => $arg2,
            'success' => $success
        ] );
    }

    public function addExpenseAction() {

        $expense = new Expense( $_POST );

        if ( $expense->saveUserExpense( $this->user->userId ) ) {
            $success = true;
            View::renderTemplate( 'Main/expense.html', [
                'user' => $this->user,
                'success' => $success
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
