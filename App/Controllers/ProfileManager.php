<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\PaymentMethod;
use \App\Models\User;

class ProfileManager extends Authenticated {

    protected function before() {
        parent::before();

        $this->user = Auth::getUser();

    }

    public function configAction($arg1='', $arg2='') {
        $incomeCategories = Income::getUserIncomeCategories( $this->user->userId );
        $expenseCategories = Expense::getUserExpenseCategories( $this->user->userId );
        $paymentMethods = PaymentMethod::getUserPaymentMethods( $this->user->userId );

        View::renderTemplate( 'Profile/config.html', [

            'user' => $this->user,
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'paymentMethods' => $paymentMethods,
            'success' => $arg1,
            'failiure' => $arg2
        ] );
    }

    public function editUserIncomeCategoryAction () {

        $currentIncomeCategoryName = $_POST['categoryHiddenName'];
        $newIncomeCategoryName = ucfirst ( $_POST['categoryName'] );        

        if ( !Income::checkIfUserIncomeCategoryExists( $this->user->userId, $newIncomeCategoryName ) ) {
            Income::editUserIncomeCategory( $this->user->userId, $currentIncomeCategoryName, $newIncomeCategoryName );
            $message = "Poprawnie zmieniono przychód";
            $error = '';
            $this -> configAction($message, $error);            
        } else {
            $message = '';
            $error = "Nie udało się zmienić przychodu";
            $this -> configAction($message, $error);
        }
    }

    public function editUserExpenseCategoryAction () {

        $currentExpenseCategoryName = $_POST['categoryHiddenName'];
        $newExpenseCategoryName = ucfirst ( $_POST['categoryName'] );

        if (isset ($_POST['expenseLimit'])) {
            $expenseLimitAmount = $_POST['limitAmount'];
            $currentExpenseCategoryName = $_POST['categoryHiddenName'];
            if ($expenseLimitAmount != Expense::getUserCategoryLimit($this->user->userId, $currentExpenseCategoryName)) {
                Expense::updateUserCategoryLimit($this->user->userId, $currentExpenseCategoryName, $expenseLimitAmount);
            }
            if ($currentExpenseCategoryName != $newExpenseCategoryName) {
                if ( !Expense::checkIfUserExpenseCategoryExists( $this->user->userId, $newExpenseCategoryName ) ) {
                    Expense::editUserExpenseCategory( $this->user->userId, $currentExpenseCategoryName, $newExpenseCategoryName );
        
                    $message = "Poprawnie zmieniono wydatek";
                    $error = '';
                    $this -> configAction($message, $error);  
                }
            }
            else {
                $message = '';
                $error = "Nie udało się zmienić wydatku";
                $this -> configAction($message, $error);
            }
        }
        else {
            if ( Expense::getUserCategoryLimit($this->user->userId, $currentExpenseCategoryName) != 0) {
                Expense::updateUserCategoryLimit($this->user->userId, $currentExpenseCategoryName, 0);
            }
            if ($currentExpenseCategoryName != $newExpenseCategoryName) {
                if ( !Expense::checkIfUserExpenseCategoryExists( $this->user->userId, $newExpenseCategoryName ) ) {
                    Expense::editUserExpenseCategory( $this->user->userId, $currentExpenseCategoryName, $newExpenseCategoryName );        
                    $message = "Poprawnie zmieniono wydatek";
                    $error = '';
                    $this -> configAction($message, $error);  
                }
            }
            else {
                $message = '';
                $error = "Nie udało się zmienić wydatku";
                $this -> configAction($message, $error);
            }
        }
    }

    public function editUserPaymentMethodAction () {

        $currentPaymentMethodName = $_POST['categoryHiddenName'];
        $newPaymentMethodName = ucfirst ( $_POST['categoryName'] );

        if ( !PaymentMethod::checkIfUserPaymentMethodExists( $this->user->userId, $newPaymentMethodName ) ) {
            PaymentMethod::editUserPaymentMethod( $this->user->userId, $currentPaymentMethodName, $newPaymentMethodName );

            $message = "Poprawnie zmieniono metodę płatności";
            $error = '';
            $this -> configAction($message, $error); 
        } else {
            $message = '';
            $error = "Nie udało się zmienić metody płatności";
            $this -> configAction($message, $error);
        }
    }

    public function addNewUserIncomeCategoryAction() {

        $newIncomeCategoryName = ucfirst ( $_POST['categoryName'] );

        if ( !Income::checkIfUserIncomeCategoryExists( $this->user->userId, $newIncomeCategoryName ) ) {
            Income::addNewUserIncomeCategory( $this->user->userId, $newIncomeCategoryName );

            $message = "Dodano nową kategorię";
            $error = '';
            $this -> configAction($message, $error);
        } else {
            $message = '';
            $error = "Podana kategoria już istnieje";
            $this -> configAction($message, $error);
        }
    }

    public function addNewUserExpenseCategoryAction() {

        $newExpenseCategoryName = ucfirst ( $_POST['categoryName'] );

        if ( !Expense::checkIfUserExpenseCategoryExists( $this->user->userId, $newExpenseCategoryName ) ) {
            if (isset ($_POST['expenseLimit'])) {
                $expenseLimitAmount = $_POST['newExpenseLimits'];
                Expense::addNewUserExpenseCategory( $this->user->userId, $newExpenseCategoryName, $expenseLimitAmount );
                $message = "Dodano nową kategorię";
                $error = '';
                $this -> configAction($message, $error);
            } else {
                Expense::addNewUserExpenseCategory( $this->user->userId, $newExpenseCategoryName, 0 );
                $message = "Dodano nową kategorię";
                $error = '';
                $this -> configAction($message, $error);
            }
        } else {
            $message = '';
            $error = "Podana kategoria już istnieje";
            $this -> configAction($message, $error);
        }
    }

    public function addNewUserPaymentMethodAction() {

        $newPaymentMethodName = ucfirst ( $_POST['categoryName'] );

        if ( !PaymentMethod::checkIfUserPaymentMethodExists( $this->user->userId, $newPaymentMethodName ) ) {
            PaymentMethod::addNewUserPaymentMethod( $this->user->userId, $newPaymentMethodName );

            $message = "Dodano nową metodę płatności";
            $error = '';
            $this -> configAction($message, $error);
        } else {
            $message = '';
            $error = "Podana metoda płatności już istnieje";
            $this -> configAction($message, $error);
        }
    }

    public function deleteUserIncomeCategoryAction() {

        $incomeCategoryName = $_POST['categoryHiddenName'];
        $option = $_POST['option'];
        if ( isset ( $_POST['categorySelect'] ) ) {
            $selectedCategoryToMoveIncomes = $_POST['categorySelect'];
        }

        if ( $option == 'delete' ) {
            if ( Income::deleteIncomesFromUserIncomeCategory( $this->user->userId, $incomeCategoryName ) ) {
                if ( Income::deleteUserIncomeCategory( $this->user->userId, $incomeCategoryName ) ) {
                    $message = "Poprawnie usunięto kategorię ".$incomeCategoryName;
                    $error = '';
                    $this -> configAction($message, $error);
                } else {
                    $message = '';
                    $error = "Nie udało się usunąć kategorii ".$incomeCategoryName;
                    $this -> configAction($message, $error);
                }
            } else {
                $message = '';
                $error = "Nie udało się usunąć kategorii ".$incomeCategoryName;
                $this -> configAction($message, $error);
            }

        } else {
            if ( Income::moveIncomesToOtherCategory ( $this->user->userId, $incomeCategoryName, $selectedCategoryToMoveIncomes ) ) {
                if ( Income::deleteIncomesFromUserIncomeCategory( $this->user->userId, $incomeCategoryName ) ) {
                    Income::deleteUserIncomeCategory( $this->user->userId, $incomeCategoryName );

                    $message = "Poprawnie usunięto kategorię ".$incomeCategoryName;
                    $error = '';
                    $this -> configAction($message, $error);

                } else {
                    $message = '';
                    $error = "Nie udało się usunąć kategorii ".$incomeCategoryName;
                    $this -> configAction($message, $error);
                }
            } else {
                $message = '';
                $error = "Nie udało się usunąć kategorii ".$incomeCategoryName;
                $this -> configAction($message, $error);
            }
        }
    }

    public function deleteUserExpenseCategoryAction() {

        $expenseCategoryName = $_POST['categoryHiddenName'];
        $option = $_POST['option'];
        if ( isset ( $_POST['categorySelect'] ) ) {
            $selectedCategoryToMoveExpense = $_POST['categorySelect'];
        }

        if ( $option == 'delete' ) {
            if ( Expense::deleteExpensesFromUserExpenseCategory( $this->user->userId, $expenseCategoryName ) ) {
                if ( Expense::deleteUserExpensesCategory( $this->user->userId, $expenseCategoryName ) ) {
                    $message = "Poprawnie usunięto kategorię ".$expenseCategoryName;
                    $error = '';
                    $this -> configAction($message, $error);
                } else {
                    $message = '';
                    $error = "Nie udało się usunąć kategorii ".$expenseCategoryName;
                    $this -> configAction($message, $error);
                }
            } else {
                $message = '';
                $error = "Nie udało się usunąć kategorii ".$expenseCategoryName;
                $this -> configAction($message, $error);
            }

        } else {
            if ( Expense::moveExpensesToOtherCategory ( $this->user->userId, $expenseCategoryName, $selectedCategoryToMoveExpense ) ) {
                if ( Expense::deleteExpensesFromUserExpenseCategory( $this->user->userId, $expenseCategoryName ) ) {
                    Expense::deleteUserExpensesCategory( $this->user->userId, $expenseCategoryName );

                    $message = "Poprawnie usunięto kategorię ".$expenseCategoryName;
                    $error = '';
                    $this -> configAction($message, $error);

                } else {
                    $message = '';
                    $error = "Nie udało się usunąć kategorii ".$expenseCategoryName;
                    $this -> configAction($message, $error);
                }
            } else {
                $message = '';
                $error = "Nie udało się usunąć kategorii ".$expenseCategoryName;
                $this -> configAction($message, $error);
            }
        }
    }

    public function deleteUserPaymentMethodAction() {

        $paymentMethodToDelete = $_POST['categoryHiddenName'];
        if ( PaymentMethod::deleteUserPaymentMethod ( $this->user->userId, $paymentMethodToDelete ) ) {
            $message = "Poprawnie usunięto metodę płatności ".$paymentMethodToDelete;
            $error = '';
            $this -> configAction($message, $error);
        } else {
            $message = '';
            $error = "Nie udało się usunąć metody płatności ".$paymentMethodToDelete;
            $this -> configAction($message, $error);
        }
    }

    public function checkUserPasswordAction() {
            
        $checked = User::validateUserPassword($this->user->userId, $_GET['password']);
        
        header('Content-Type: application/json');
        echo json_encode($checked);
    }

    public function changeUserDataAction () {
       
        if ((isset($_POST['newPassword'])) && (!empty($_POST['newPassword']))) {
            $newPassword = $_POST['newPassword'];
        }
        if ($_POST['username'] != $this->user->username) {
            $userName = $_POST['username'];
        }
        else {
            $userName = $this->user->username;
        }
        if ($_POST['email'] != $this->user->email) {
            $userEmail = $_POST['email'];
        }
        else {
            $email = $this->user->email;
        }

        if (!User::changeUserData ($this->user->userId, $userName, $email)) {
            $message = '';
            $error = "Nie udało się przetworzyć zapytania";
            $this -> configAction($message, $error);
        } else {
            if (isset($newPassword)) {
                if (!User::changeUserPassword ($this->user->userId, $newPassword)) {
                    $message = '';
                    $error = "Nie udało się przetworzyć zapytania";
                    $this -> configAction($message, $error);
                }
                else {
                    $message = "Zapisano nowe dane";
                    $error = '';
                    $this -> configAction($message, $error);
                }
            } else {
                $message = "Zapisano nowe dane";
                $error = '';
                $this -> configAction($message, $error);
            }
        }
    }
}