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
        $newIncomeCategoryName = ucfirst ( $_POST['categoryName'] );

        if ( !Income::checkIfUserIncomeCategoryExists( $this->user->userId, $newIncomeCategoryName ) ) {
            Income::editUserIncomeCategory( $this->user->userId, $currentIncomeCategoryName, $newIncomeCategoryName );

            $this->redirect( '/config' );
        } else {
            echo 'blad';
            //work in progress
        }
    }

    public function editUserExpenseCategoryAction () {

        $currentExpenseCategoryName = $_POST['categoryHiddenName'];
        $newExpenseCategoryName = ucfirst ( $_POST['categoryName'] );

        if ( !Expense::checkIfUserExpenseCategoryExists( $this->user->userId, $newExpenseCategoryName ) ) {
            Expense::editUserExpenseCategory( $this->user->userId, $currentExpenseCategoryName, $newExpenseCategoryName );

            $this->redirect( '/config' );
        } else {
            echo 'blad';
            //work in progress
        }
    }

    public function editUserPaymentMethodAction () {

        $currentPaymentMethodName = $_POST['categoryHiddenName'];
        $newPaymentMethodName = ucfirst ( $_POST['categoryName'] );

        if ( !PaymentMethod::checkIfUserPaymentMethodExists( $this->user->userId, $newPaymentMethodName ) ) {
            PaymentMethod::editUserPaymentMethod( $this->user->userId, $currentPaymentMethodName, $newPaymentMethodName );

            $this->redirect( '/config' );
        } else {
            echo 'blad';
            //work in progress
        }
    }

    public function addNewUserIncomeCategoryAction() {

        $newIncomeCategoryName = ucfirst ( $_POST['categoryName'] );

        if ( !Income::checkIfUserIncomeCategoryExists( $this->user->userId, $newIncomeCategoryName ) ) {
            Income::addNewUserIncomeCategory( $this->user->userId, $newIncomeCategoryName );

            $this->redirect( '/config' );
        } else {
            echo 'Istnieje';
            //work in progress
        }
    }

    public function addNewUserExpenseCategoryAction() {

        $newExpenseCategoryName = ucfirst ( $_POST['categoryName'] );

        if ( !Expense::checkIfUserExpenseCategoryExists( $this->user->userId, $newExpenseCategoryName ) ) {
            Expense::addNewUserExpenseCategory( $this->user->userId, $newExpenseCategoryName );

            $this->redirect( '/config' );
        } else {
            echo 'Istnieje';
            //work in progress
        }
    }

    public function addNewUserPaymentMethodAction() {

        $newPaymentMethodName = ucfirst ( $_POST['categoryName'] );

        if ( !PaymentMethod::checkIfUserPaymentMethodExists( $this->user->userId, $newPaymentMethodName ) ) {
            PaymentMethod::addNewUserPaymentMethod( $this->user->userId, $newPaymentMethodName );

            $this->redirect( '/config' );
        } else {
            echo 'Istnieje';
            //work in progress
        }
    }

    public function deleteUserIncomeCategoryAction() {

        $incomeCategoryName = $_POST['categoryHiddenName'];
        $option = $_POST['option'];
        if (isset ($_POST['categorySelect'])) {
            $selectedCategoryToMoveIncomes = $_POST['categorySelect'];
        }        

        if ( $option == 'delete' ) {
            if ( Income::deleteIncomesFromUserIncomeCategory( $this->user->userId, $incomeCategoryName ) ) {
                if ( Income::deleteUserIncomeCategory( $this->user->userId, $incomeCategoryName ) ) {
                    $this->redirect( '/config' );
                } else {
                    echo 'Blad';
                    //work in progress
                }
            } else {
                echo 'Blad';
                //work in progress
            }

        } else {
            if ( Income::moveIncomesToOtherCategory ( $this->user->userId, $incomeCategoryName, $selectedCategoryToMoveIncomes ) ) {
                if ( Income::deleteIncomesFromUserIncomeCategory( $this->user->userId, $incomeCategoryName ) ) {
                    Income::deleteUserIncomeCategory( $this->user->userId, $incomeCategoryName );
                    
                    $this->redirect( '/config' );

                } else {
                    echo 'Blad';
                    //work in progress
                }
            } else {
                echo 'Blad';
                //work in progress
            }
        }
    }
    
    public function deleteUserExpenseCategoryAction() {

        $expenseCategoryName = $_POST['categoryHiddenName'];
        $option = $_POST['option'];
        if (isset ($_POST['categorySelect'])) {
            $selectedCategoryToMoveExpense = $_POST['categorySelect'];
        }        

        if ( $option == 'delete' ) {
            if ( Expense::deleteExpensesFromUserExpenseCategory( $this->user->userId, $expenseCategoryName ) ) {
                if ( Expense::deleteUserExpensesCategory( $this->user->userId, $expenseCategoryName ) ) {
                    $this->redirect( '/config' );
                } else {
                    echo 'Blad';
                    //work in progress
                }
            } else {
                echo 'Blad';
                //work in progress
            }

        } else {
            if ( Expense::moveExpensesToOtherCategory ( $this->user->userId, $expenseCategoryName, $selectedCategoryToMoveExpense ) ) {
                if ( Expense::deleteExpensesFromUserExpenseCategory( $this->user->userId, $expenseCategoryName ) ) {
                    Expense::deleteUserExpensesCategory( $this->user->userId, $expenseCategoryName );
                    
                    $this->redirect( '/config' );

                } else {
                    echo 'Blad';
                    //work in progress
                }
            } else {
                echo 'Blad';
                //work in progress
            }
        }
    }
}