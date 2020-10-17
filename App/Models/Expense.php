<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Validation;

class Expense extends \Core\Model {


    public function __construct( $data = [] ) {
        foreach ( $data as $key => $value ) {
            $this->$key = $value;
        }
    }

    public function saveUserExpense( $userId ) {
        $this->amount = str_replace( [','], ['.'], $this->amount );
        $this->amountErrors = Validation::validateAmount( $this->amount );
        $this->dateErrors = Validation::validateDate( $this->date );
        
        if ( ( empty( $this->amountErrors ) ) && ( empty( $this->dateErrors ) ) ) {

            $sql = 'INSERT INTO expenses VALUES(NULL, :userId, :userExpenseCategoryId, :userPaymentMethodId, :amount, :expenseDate, :expenseComment)';

            $db = static::getDB();
            $stmt = $db->prepare( $sql );
            $comment = htmlentities( htmlentities( $this->comment, ENT_QUOTES, 'UTF-8' ) );

            $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
            $stmt->bindValue( ':userExpenseCategoryId', $this->getUserCategoryId( $userId ), PDO::PARAM_INT );
            $stmt->bindValue( ':userPaymentMethodId', $this->getUserPaymentMethodId( $userId ), PDO::PARAM_INT );
            $stmt->bindValue( ':amount', $this->amount, PDO::PARAM_STR );
            $stmt->bindValue( ':expenseDate', $this-> date, PDO::PARAM_STR );
            $stmt->bindValue( ':expenseComment', $comment, PDO::PARAM_STR );

            return $stmt->execute();
        }

        return false;
    }

    public function getUserCategoryId( $userId ) {

        $sql = 'SELECT id, userId, name FROM user_expense_categories WHERE userId = :userId AND name = :name';

        $db = static::getDB();

        $stmt = $db->prepare( $sql );
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $this->category, PDO::PARAM_STR );
        $stmt->execute();

        $categories = $stmt -> fetch();

        return $categories['id'];

    }

    public function getUserPaymentMethodId( $userId ) {

        $sql = 'SELECT id, userId, name FROM user_payment_methods WHERE userId = :userId AND name = :name';

        $db = static::getDB();

        $stmt = $db->prepare( $sql );
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $this->paymentMethod, PDO::PARAM_STR );
        $stmt->execute();

        $paymentMethods = $stmt -> fetch();

        return $paymentMethods['id'];

    }    

    public static function getExpenses( $date, $userId ) {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT expenses.amount, expenses.expenseDate, expenses.userExpenseCategoryId, expenses.userPaymentMethodId, expenses.expenseComment, uec.name FROM expenses, user_expense_categories AS uec WHERE expenseDate BETWEEN :beginDate AND :endDate AND expenses.userId = :userId AND uec.id = expenses.userExpenseCategoryId ORDER BY expenses.expenseDate DESC' );

        $stmt->bindValue( ':beginDate', $date['beginDate'], PDO::PARAM_STR );
        $stmt->bindValue( ':endDate', $date['endDate'], PDO::PARAM_STR );
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_ASSOC );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getExpensesTotal( $date, $userId ) {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT ROUND(SUM(expenses.amount), 2), expenses.userExpenseCategoryId, uec.name FROM expenses expenses, user_expense_categories AS uec WHERE expenses.expenseDate BETWEEN :beginDate AND :endDate AND expenses.userId = :userId AND uec.id = expenses.userExpenseCategoryId GROUP BY expenses.userExpenseCategoryId' );

        $stmt->bindValue( ':beginDate', $date['beginDate'], PDO::PARAM_STR );
        $stmt->bindValue( ':endDate', $date['endDate'], PDO::PARAM_STR );
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_ASSOC );

        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    public static function getUserExpenseCategories( $userId ) {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT name FROM user_expense_categories WHERE userId = :userId' );

        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function editUserExpenseCategory ( $userId, $currentExpenseCategoryName, $newExpenseCategoryName ) {        
        
        $categoryId = static::getUserExpenseCategoryId($userId, $currentExpenseCategoryName);
        
        $db = static::getDB();
        
        $stmt = $db->prepare( 'UPDATE user_expense_categories SET name = :name WHERE id = :id ' );

        $stmt->bindValue( ':id', $categoryId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newExpenseCategoryName, PDO::PARAM_STR );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        
        return $stmt->execute();
         
    }
    
    public static function getUserExpenseCategoryId ( $userId, $currentExpenseCategoryName ) {
        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT id FROM user_expense_categories WHERE name =:name AND userId =:userId ' );

        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $currentExpenseCategoryName, PDO::PARAM_STR );
        
        $stmt->execute();

        return $stmt->fetchColumn();
    }
    
    public static function checkIfUserExpenseCategoryExists($userId, $newExpenseCategoryName) {
        
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM user_expense_categories WHERE userId = :userId AND name =:name' );

        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newExpenseCategoryName, PDO::PARAM_STR );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
     public static function addNewUserExpenseCategory($userId, $newExpenseCategoryName) {
       
        $db = static::getDB();
        
        $stmt = $db->prepare('INSERT INTO user_expense_categories VALUES (NULL, :userId, :name)');
        
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newExpenseCategoryName, PDO::PARAM_STR );        
        
        return $stmt->execute();
    }
}
