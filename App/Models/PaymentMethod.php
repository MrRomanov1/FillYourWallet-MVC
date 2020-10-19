<?php

namespace App\Models;

use PDO;
use \Core\View;

class PaymentMethod extends \Core\Model {

    
    public function __construct( $data = [] ) {
        foreach ( $data as $key => $value ) {
            $this->$key = $value;
        }
    }
    
    public static function getUserPaymentMethods( $userId ) {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT name FROM user_payment_methods WHERE userId = :userId' );

        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    public static function editUserPaymentMethod ( $userId, $currentPaymentMethodName, $newPaymentMethodName ) {        
        
        $categoryId = static::getUserPaymentMethodId($userId, $currentPaymentMethodName);
        
        $db = static::getDB();
        
        $stmt = $db->prepare( 'UPDATE user_payment_methods SET name = :name WHERE id = :id ' );

        $stmt->bindValue( ':id', $categoryId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newPaymentMethodName, PDO::PARAM_STR );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        
        return $stmt->execute();
         
    }
    
    public static function getUserPaymentMethodId ( $userId, $currentPaymentMethodName ) {
        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT id FROM user_payment_methods WHERE name =:name AND userId =:userId ' );

        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $currentPaymentMethodName, PDO::PARAM_STR );
        
        $stmt->execute();

        return $stmt->fetchColumn();
    }
    
    public static function checkIfUserPaymentMethodExists($userId, $newPaymentMethodName) {
        
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM user_payment_methods WHERE userId = :userId AND name =:name' );

        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newPaymentMethodName, PDO::PARAM_STR );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    public static function addNewUserPaymentMethod($userId, $newPaymentMethodName) {
       
        $db = static::getDB();
        
        $stmt = $db->prepare('INSERT INTO user_payment_methods VALUES (NULL, :userId, :name)');
        
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newPaymentMethodName, PDO::PARAM_STR );        
        
        return $stmt->execute();
    }
    
    public static function deleteUserPaymentMethod ($userId, $paymentMethodToDelete) {
        $db = static::getDB();
        
        $stmt = $db->prepare('DELETE FROM user_payment_methods WHERE userId = :userId AND name =:name');
        
        $stmt->bindValue( ':userId', $userId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $paymentMethodToDelete, PDO::PARAM_STR );        
        
        return $stmt->execute();
    }
}

