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
            $comment = filter_var( $this->comment, FILTER_SANITIZE_SPECIAL_CHARS);

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

        $stmt = $db->prepare( 'SELECT ROUND(SUM(expenses.amount), 2), expenses.userExpenseCategoryId, uec.name FROM expenses, user_expense_categories AS uec WHERE expenses.expenseDate BETWEEN :beginDate AND :endDate AND expenses.userId = :userId AND uec.id = expenses.userExpenseCategoryId GROUP BY expenses.userExpenseCategoryId' );

        $stmt->bindValue( ':beginDate', $date['beginDate'], PDO::PARAM_STR );
        $stmt->bindValue( ':endDate', $date['endDate'], PDO::PARAM_STR );
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_ASSOC );

        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    public static function getUserExpenseCategories( $userId ) {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT name, expenseLimit FROM user_expense_categories WHERE userId = :userId' );

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
    
    public static function getUserExpenseCategoryId ( $userId, $expenseCategoryName ) {
        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT id FROM user_expense_categories WHERE name =:name AND userId =:userId ' );

        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $expenseCategoryName, PDO::PARAM_STR );
        
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
    
     public static function addNewUserExpenseCategory($userId, $newExpenseCategoryName, $expenseLimit) {
       
        $db = static::getDB();
        
        $stmt = $db->prepare('INSERT INTO user_expense_categories VALUES (NULL, :userId, :name, :expenseLimit)');
        
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newExpenseCategoryName, PDO::PARAM_STR );
        $stmt->bindValue( ':expenseLimit', $expenseLimit, PDO::PARAM_INT );        
        
        return $stmt->execute();
    }
    
    public static function deleteUserExpensesCategory($userId, $categoryName) {
                    
        $db = static::getDB();
        
        $stmt = $db->prepare('DELETE FROM user_expense_categories WHERE userId = :userId AND name =:name');
        
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $categoryName, PDO::PARAM_STR );        
        
        return $stmt->execute();
    }
    
    public static function deleteExpensesFromUserExpenseCategory($userId, $categoryName) {
       
        $categoryId = static::getUserExpenseCategoryId($userId, $categoryName); 
        
        $db = static::getDB();
        
        $stmt = $db->prepare('DELETE FROM expenses WHERE userId = :userId AND userExpenseCategoryId =:userExpenseCategoryId');
        
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':userExpenseCategoryId', $categoryId, PDO::PARAM_INT );        
        
        return $stmt->execute();
    }
    
    public static function moveExpensesToOtherCategory($userId, $categoryName, $selectedCategoryToMoveExpenses ) {
        
        $categoryId = static::getUserExpenseCategoryId($userId, $categoryName);
        $categoryIdToMoveExpenses = static::getUserExpenseCategoryId($userId, $selectedCategoryToMoveExpenses);
        $userExpenses = static::getUserExpensesFromCategory($userId, $categoryId);                
        
        $db = static::getDB();
        
        foreach ($userExpenses as $expense) {
            
            $stmt = $db->prepare('INSERT INTO expenses VALUES(NULL, :userId, :userExpenseCategoryId, :userPaymentMethodId, :amount, :expenseDate, :expenseComment)');
            
            $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
            $stmt->bindValue( ':userExpenseCategoryId', $categoryIdToMoveExpenses, PDO::PARAM_INT );            
            $stmt->bindValue( ':userPaymentMethodId', $expense['userPaymentMethodId'], PDO::PARAM_INT );            
            $stmt->bindValue( ':amount', $expense['amount'], PDO::PARAM_STR );
            $stmt->bindValue( ':expenseDate', $expense['expenseDate'], PDO::PARAM_STR );
            $stmt->bindValue( ':expenseComment', $expense['expenseComment'], PDO::PARAM_STR );
            
            $stmt->execute();
        }
        return true;
    }
    
    public static function getUserExpensesFromCategory ($userId, $categoryId) {
        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT * FROM expenses WHERE userId = :userId AND userExpenseCategoryId =:userExpenseCategoryId' );
        
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':userExpenseCategoryId', $categoryId, PDO::PARAM_INT ); 
        
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public static function getUserCategoryLimit ($userId, $categoryName) {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT expenseLimit FROM user_expense_categories WHERE userId = :userId AND name =:name' );

        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $categoryName, PDO::PARAM_STR );
        
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public static function updateUserCategoryLimit ($userId, $categoryName, $newCategoryLimit) {
        $db = static::getDB();

        $stmt = $db->prepare( 'UPDATE user_expense_categories SET expenseLimit = :expenseLimit WHERE name = :name AND userId =:userId' );

        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $categoryName, PDO::PARAM_STR );
        $stmt->bindValue( ':expenseLimit', $newCategoryLimit, PDO::PARAM_INT );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        
        return $stmt->execute();
    }

    public static function getSingleCategoryExpenses ($date, $userId, $categoryId) {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM expenses WHERE userId = :userId AND userExpenseCategoryId =:userExpenseCategoryId AND expenseDate BETWEEN :beginDate AND :endDate ORDER BY expenseDate DESC' );
        
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':beginDate', $date['beginDate'], PDO::PARAM_STR );
        $stmt->bindValue( ':endDate', $date['endDate'], PDO::PARAM_STR );
        $stmt->bindValue( ':userExpenseCategoryId', $categoryId, PDO::PARAM_INT ); 
        
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public static function getSingleExpenseData ($expenseId) {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM expenses WHERE id = :id');

        $stmt->bindValue( ':id', $expenseId, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function editSingleExpense($expenseId, $expenseComment, $amount, $expenseDate) {
        $db = static::getDB();

        $amount = str_replace( [','], ['.'], $amount );

        $stmt = $db->prepare( 'UPDATE expenses SET amount = :amount, expenseDate =:expenseDate, expenseComment =:expenseComment WHERE id = :id' );

        $stmt->bindValue( ':id', $expenseId, PDO::PARAM_INT );
        $stmt->bindValue( ':expenseDate', $expenseDate, PDO::PARAM_STR );
        $stmt->bindValue( ':amount', $amount, PDO::PARAM_STR );
        $stmt->bindValue( ':expenseComment', $expenseComment, PDO::PARAM_STR); 

        return $stmt->execute();
    }

    public static function moveSingleExpenseToOtherCategory ($userId, $expenseId, $categoryToMove) {
        $categoryId = static::getUserExpenseCategoryId($userId, $categoryToMove);                     
        
        $db = static::getDB();        
        
            
        $stmt = $db->prepare('UPDATE expenses SET userExpenseCategoryId =:userExpenseCategoryId WHERE id = :id');
        
        $stmt->bindValue( ':id', $expenseId, PDO::PARAM_INT );
        $stmt->bindValue( ':userExpenseCategoryId', $categoryId, PDO::PARAM_INT );             
        
    
        return $stmt->execute();
    }

    public static function deleteSingleExpense ($expenseId) {
        $db = static::getDB(); 
        
        $stmt = $db->prepare('DELETE FROM expenses WHERE id = :id');

        $stmt->bindValue( ':id', $expenseId, PDO::PARAM_INT );

        return $stmt->execute();
    }

    
}
