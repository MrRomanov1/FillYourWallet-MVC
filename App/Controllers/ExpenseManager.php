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

    public function viewPageAction() {
        View::renderTemplate( 'Main/expense.html', [
            'user' => $this->user
        ] );
    }

    public function addExpenseAction() {

        $expense = new Expense( $_POST );

        if ( $expense->saveUserExpense( $this->user->userId ) ) {
            echo 'dodane';
            ;
        } else {
            echo 'niedodane';
            ;
        }
    }
}
