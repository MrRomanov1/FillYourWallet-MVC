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

}
