<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Validation;

class Income extends \Core\Model {

    public $amountErrors = [];
    public $dateErrors = [];

    public function __construct( $data = [] ) {
        foreach ( $data as $key => $value ) {
            $this->$key = $value;
        }
    }

    public function saveUserIncome( $userId ) {
        $this->amount = str_replace( [','], ['.'], $this->amount );
        $this->amountErrors = Validation::validateAmount( $this->amount );
        $this->dateErrors = Validation::validateDate( $this->date );

        if ( ( empty( $this->amountErrors ) ) && ( empty( $this->dateErrors ) ) ) {

            $sql = 'INSERT INTO incomes VALUES(NULL, :userId, :userIncomeCategoryId, :amount, :incomeDate, :incomeComment)';

            $db = static::getDB();
            $stmt = $db->prepare( $sql );
            $comment = htmlentities( htmlentities( $this->comment, ENT_QUOTES, 'UTF-8' ) );

            $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
            $stmt->bindValue( ':userIncomeCategoryId', $this->getUserCategoryId( $userId ), PDO::PARAM_INT );
            $stmt->bindValue( ':amount', $this->amount, PDO::PARAM_STR );
            $stmt->bindValue( ':incomeDate', $this-> date, PDO::PARAM_STR );
            $stmt->bindValue( ':incomeComment', $comment, PDO::PARAM_STR );

            return $stmt->execute();
        }

        return false;
    }

    public function getUserCategoryId( $userId ) {

        $sql = 'SELECT id, userId, name FROM user_income_categories WHERE userId = :userId AND name = :name';

        $db = static::getDB();

        $stmt = $db->prepare( $sql );
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $this->category, PDO::PARAM_STR );
        $stmt->execute();

        $categories = $stmt -> fetch();

        return $categories['id'];

    }

    public static function getIncomes( $date, $userId ) {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT amount, incomeDate, userIncomeCategoryId, incomeComment, uic.name FROM incomes, user_income_categories AS uic WHERE incomes.incomeDate BETWEEN :beginDate AND :endDate AND incomes.userId = :userId AND incomes.userIncomeCategoryId = uic.id ORDER BY incomes.incomeDate ASC' );

        $stmt->bindValue( ':beginDate', $date['beginDate'], PDO::PARAM_STR );
        $stmt->bindValue( ':endDate', $date['endDate'], PDO::PARAM_STR );
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_ASSOC );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getIncomesTotal( $date, $userId ) {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT ROUND(SUM(incomes.amount), 2), uic.name FROM incomes, user_income_categories AS uic WHERE incomes.incomeDate BETWEEN :beginDate AND :endDate AND incomes.userId = :userId AND incomes.userIncomeCategoryId = uic.id GROUP BY incomes.userIncomeCategoryId' );

        $stmt->bindValue( ':beginDate', $date['beginDate'], PDO::PARAM_STR );
        $stmt->bindValue( ':endDate', $date['endDate'], PDO::PARAM_STR );
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_ASSOC );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getUserIncomeCategories( $userId ) {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT name FROM user_income_categories WHERE userId = :userId' );

        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    public static function editUserIncomeCategory ( $userId, $currentIncomeCategoryName, $newIncomeCategoryName ) {        
        
        $categoryId = static::getUserIncomeCategoryId($userId, $currentIncomeCategoryName);
        
        $db = static::getDB();
        
        $stmt = $db->prepare( 'UPDATE user_income_categories SET name = :name WHERE id = :id ' );

        $stmt->bindValue( ':id', $categoryId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newIncomeCategoryName, PDO::PARAM_STR );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        
        return $stmt->execute();
         
    }
    
    public static function getUserIncomeCategoryId ( $userId, $currentIncomeCategoryName ) {
        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT id FROM user_income_categories WHERE name =:name AND userId =:userId ' );

        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $currentIncomeCategoryName, PDO::PARAM_STR );
        
        $stmt->execute();

        return $stmt->fetchColumn();
    }
    
    public static function checkIfUserIncomeCategoryExists($userId, $currentIncomeCategoryName) {
        
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM user_income_categories WHERE userId = :userId AND name =:name' );

        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $currentIncomeCategoryName, PDO::PARAM_STR );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
