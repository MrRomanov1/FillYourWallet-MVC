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
    
    public function editUserIncomeCategoryAction () {
        
        $currentIncomeCategoryName = $_POST['categoryHiddenName'];
        $newIncomeCategoryName = ucfirst ($_POST['categoryName']);
        
        if (!Income::checkIfUserIncomeCategoryExists($this->user->userId, $newIncomeCategoryName )){
            Income::editUserIncomeCategory($this->user->userId, $currentIncomeCategoryName, $newIncomeCategoryName);
            
            $this->redirect('/config');
        }
        else {
            echo "blad";
        }
    }
    
    public function editUserExpenseCategoryAction () {
        
        $currentExpenseCategoryName = $_POST['categoryHiddenName'];
        $newExpenseCategoryName = ucfirst ($_POST['categoryName']);
        
        if (!Expense::checkIfUserExpenseCategoryExists($this->user->userId, $newExpenseCategoryName )){
            Expense::editUserExpenseCategory($this->user->userId, $currentExpenseCategoryName, $newExpenseCategoryName);
            
            $this->redirect('/config');
        }
        else {
            echo "blad";
        }
    }

}
