<?php

namespace App\Models;

use PDO;
use DateTime;
use \Core\View;

class Income extends \Core\Model {

    public $errors = [];

    public function __construct( $data = [] ) {
        foreach ( $data as $key => $value ) {
            $this->$key = $value;
        }
    }

    public function saveUserIncome( $userId ) {
        $this->validateNewData();        

        if ( empty( $this->errors ) ) { 
            
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

    public function validateNewData() {

        // amount validation
        $this->amount = str_replace( [','], ['.'], $this->amount );

        if ( empty( $this->amount ) ) {
            $this->errors[] = 'Wpisz kwotę!';            
        }
        if ( !filter_var( $this->amount, FILTER_VALIDATE_FLOAT ) ) {
            $this->errors[] = 'Niepoprawna kwota!';            
        }
        if ( ( float )$this->amount < 0 ) {
            $this->errors[] = 'Kwota nie może być ujemna!';            
        }

        // date validation
        $setDate = DateTime::createFromFormat( 'Y-m-d', $this-> date );

        if ( empty( $this-> date ) ) {
            $this->errors[] = 'Wybierz datę!';            
        }
        if ( !$setDate ) {

            $this->errors[] = 'Podaj datę w formacie dd.mm.rrrr!';             

        }
        $currentDate = new DateTime();

        $year = $currentDate -> format( 'Y' );
        $month = $currentDate -> format( 'm' );
        $day = $currentDate -> format( 'd' );
        $currentMonthLength = cal_days_in_month( CAL_GREGORIAN, $month, $year );
        $currentMonthBegin = DateTime::createFromFormat( 'Y-m-d', $year.'-01-01' );
        $currentMonthEnd = DateTime::createFromFormat( 'Y-m-d', $year.'-'.$month.'-'.$currentMonthLength );

        if ( !checkdate( $month, $day, $year ) || $setDate < $currentMonthBegin || $setDate > $currentMonthEnd ) {
            $this->errors[] = 'Wprowadź datę najpóźniej do końca miesiąca!';

        }

    }

}
