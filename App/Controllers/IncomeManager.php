<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Validation;

class IncomeManager extends Authenticated {

    protected function before() {
        parent::before();

        $this->user = Auth::getUser();

    }

    public function viewPageAction( $arg1 = 0, $arg2 = 0 ) {
        $success = false;

        $userIncomeCategories = Income::getUserIncomeCategories( $this->user->userId );

        View::renderTemplate( 'Main/income.html', [
            'user' => $this->user,
            'incomes' => $arg1,
            'errors' => $arg2,
            'success' => $success,
            'userIncomeCategories' => $userIncomeCategories
        ] );
    }

    public function addIncomeAction() {

        $income = new Income( $_POST );

        if ( $income->saveUserIncome( $this->user->userId ) ) {
            $success = true;
            $userIncomeCategories = Income::getUserIncomeCategories( $this->user->userId );
            View::renderTemplate( 'Main/income.html', [
                'user' => $this->user,
                'success' => $success,
                'userIncomeCategories' => $userIncomeCategories
            ] );

        } else {
            $incomes['amount'] = $_POST['amount'];
            $incomes['date'] = $_POST['date'];

            if ( isset( $_POST['comment'] ) ) {
                $incomes['comment'] = $_POST['comment'];
            }
            $errors['dateError'] = $income -> dateErrors;
            $errors['amountError'] = $income -> amountErrors;

            $this -> viewPageAction( $incomes, $errors );

        }
    }

}
