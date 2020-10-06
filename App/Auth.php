<?php

namespace App;

use App\Models\User;

class Auth {

    public static function login( $user ) {
        session_regenerate_id( true );

        $_SESSION['userId'] = $user->userId;        

    }

    public static function logout() {

        $_SESSION = [];

        if ( ini_get( 'session.use_cookies' ) ) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

    }

    public static function rememberRequestedPage() {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    public static function getReturnToPage() {
        return $_SESSION['return_to'] ?? '/';
    }

    public static function getUser() {
        if ( isset( $_SESSION['userId'] ) ) {

            return User::findByID( $_SESSION['userId'] );
        }

    }

}
