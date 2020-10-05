<?php

namespace App\Models;

use PDO;
use \Core\View;

class User extends \Core\Model {

    public $errors = [];

    public function __construct( $data = [] ) {
        foreach ( $data as $key => $value ) {
            $this->$key = $value;
        }
    }

    public function saveUserModel() {
        $this->validateUserData();

        if ( empty( $this->errors ) ) {

            $password = password_hash( $this->password, PASSWORD_DEFAULT );

            $sql = 'INSERT INTO users VALUES(NULL, :username, :password, :email)';

            $db = static::getDB();
            $stmt = $db->prepare( $sql );

            $stmt->bindValue( ':username', $this->username, PDO::PARAM_STR );
            $stmt->bindValue( ':email', $this->email, PDO::PARAM_STR );
            $stmt->bindValue( ':password', $password, PDO::PARAM_STR );

            return $stmt->execute();
        }

        return false;
    }
    
    public function saveUserData() {
        $this->saveUserIncomeCategories();
        $this->saveUserExpenseCategories();
        $this->saveUserPaymentMethods();
    }
    
    public function saveUserIncomeCategories() {
        
            $sql = 'INSERT INTO user_expense_categories(userId, name) 
                    SELECT (SELECT userId FROM users WHERE email=:email), name 
                    FROM expenses_category_default';

            $db = static::getDB();
            $stmt = $db->prepare( $sql );
            
            $stmt->bindValue( ':email', $this->email, PDO::PARAM_STR );            

            return $stmt->execute();
    }
    
    public function saveUserExpenseCategories() {
        
            $sql = 'INSERT INTO user_income_categories(userId, name) 
                    SELECT (SELECT userId FROM users WHERE email=:email), name 
                    FROM incomes_category_default';

            $db = static::getDB();
            $stmt = $db->prepare( $sql );
            
            $stmt->bindValue( ':email', $this->email, PDO::PARAM_STR );            

            return $stmt->execute();
    }
    
     public function saveUserPaymentMethods() {
        
            $sql = 'INSERT INTO user_payment_methods(userId, name) 
                    SELECT (SELECT userId FROM users WHERE email=:email), name 
                    FROM payment_methods_default';

            $db = static::getDB();
            $stmt = $db->prepare( $sql );
            
            $stmt->bindValue( ':email', $this->email, PDO::PARAM_STR );            

            return $stmt->execute();
    }

    public function validateUserData() {

        // name validation
        if ( $this->username == '' ) {
            $this->errors[] = 'Podaj imię!';
        }

        if ( mb_strlen( $this->username ) <= 2 ) {
            $this->errors[] = 'Imię powinno posiadać co najmniej 2 znaki';
        }

        // email validation
        if ( filter_var( $this->email, FILTER_VALIDATE_EMAIL ) === false ) {
            $this->errors[] = 'Nieprawidłowy adres email!';
        }
        if ( static::emailExists( $this->email, $this->id ?? null ) ) {
            $this->errors[] = 'Adres email jest już zajęty';
        }

        // password validation
        if ( isset( $this->password ) ) {

            if ( ( strlen( $this->password ) < 6 ) || ( strlen( $this->password ) ) > 20 ) {
                $this->errors[] = 'Hasło musi posiadać od 6 do 20 znaków!';
            }

            if ( preg_match( '/.*[a-z]+.*/i', $this->password ) == 0 ) {
                $this->errors[] = 'Hasło musi posiadać co najmniej jedną literę';
            }

            if ( preg_match( '/.*\d+.*/i', $this->password ) == 0 ) {
                $this->errors[] = 'Hasło musi posiadać co najmniej jedną cyfrę';
            }

            if ( $this->password != $this->repeatedPassword ) {
                $this->errors[] = 'Podane hasła nie są identyczne!';
            }
        }
    }

    public static function emailExists( $email, $ignore_id = null ) {
        $user = static::findByEmail( $email );

        if ( $user ) {
            if ( $user->userId != $ignore_id ) {
                return true;
            }
        }

        return false;
    }

    public static function findByEmail( $email ) {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare( $sql );
        $stmt->bindValue( ':email', $email, PDO::PARAM_STR );

        $stmt->setFetchMode( PDO::FETCH_CLASS, get_called_class() );

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function authenticateUser( $email, $password ) {
        $user = static::findByEmail( $email );

        if ( $user ) {
            if ( password_verify( $password, $user->password ) ) {
                return $user;
            }
        }

        return false;
    }

    public static function findByID( $userId ) {
        $sql = 'SELECT * FROM users WHERE userId = :userId';

        $db = static::getDB();
        $stmt = $db->prepare( $sql );
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_CLASS, get_called_class() );

        $stmt->execute();

        return $stmt->fetch();
    }

}
