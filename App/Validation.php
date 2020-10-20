<?php

namespace App;

use DateTime;

class Validation {

    public $amountErrors = [];
    public $dateErrors = [];

    public static function validateAmount( $amount ) {

        if ( empty( $amount ) ) {
            $amountErrors[] = 'Wpisz kwotę!';

        }
        if ( !filter_var( $amount, FILTER_VALIDATE_FLOAT ) ) {
            $amountErrors[] = 'Niepoprawna kwota!';

        }
        if ( ( float )$amount < 0 ) {
            $amountErrors[] = 'Kwota nie może być ujemna!';

        }
        if ( isset ( $amountErrors ) ) {
            return $amountErrors;
        }
        return false;

    }

    public static function validateDate( $date ) {

        $setDate = DateTime::createFromFormat( 'Y-m-d', $date );

        if ( empty( $date ) ) {
            $dateErrors[] = 'Wybierz datę!';

        }
        if ( !$setDate ) {

            $dateErrors[] = 'Podaj datę w formacie dd.mm.rrrr!';

        }
        $currentDate = new DateTime();

        $year = $currentDate -> format( 'Y' );
        $month = $currentDate -> format( 'm' );
        $day = $currentDate -> format( 'd' );
        $currentMonthLength = cal_days_in_month( CAL_GREGORIAN, $month, $year );
        $currentMonthBegin = DateTime::createFromFormat( 'Y-m-d', $year.'-01-01' );
        $currentMonthEnd = DateTime::createFromFormat( 'Y-m-d', $year.'-'.$month.'-'.$currentMonthLength );

        if ( !checkdate( $month, $day, $year ) || $setDate < $currentMonthBegin || $setDate > $currentMonthEnd ) {
            $dateErrors[] = 'Wprowadź datę najpóźniej do końca miesiąca!';

        }
        if ( isset ( $dateErrors ) ) {
            return $dateErrors;
        }
        return false;

    }
    
    public static function validateDateOrder ($beginDate, $endDate) {
        
        if ($beginDate <= $endDate ) {
            return true;
        }
        return false;
    }

    public static function getCurrentDate() {
        $currentDate = new DateTime();         

        return $currentDate -> format('Y-m-d');
    }
}
