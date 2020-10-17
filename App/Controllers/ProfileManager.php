<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\PaymentMethod;

class ProfileManager extends Authenticated {

    protected function before() {
        parent::before();

        $this->user = Auth::getUser();

    }

    public function configAction() {
        $incomeCategories = Income::getUserIncomeCategories( $this->user->userId );
        $expenseCategories = Expense::getUserExpenseCategories( $this->user->userId );
        $paymentMethods = PaymentMethod::getUserPaymentMethods( $this->user->userId );

        View::renderTemplate( 'Profile/config.html', [

            'user' => $this->user,
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'paymentMethods' => $paymentMethods
        ] );
    }

}
