<?php

namespace Core;

use PDO;
use App\Config;

abstract class Model {

    protected static function getDB() {
        static $db = null;

        if ( $db === null ) {
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
            $db = new PDO( $dsn, Config::DB_USER, Config::DB_PASSWORD, [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES `utf8` COLLATE `utf8_polish_ci`"] );

            $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
        return $db;
    }
}
