<?php

namespace App\Controllers;

use \Core\View;
//use \App\Auth;

class Home extends \Core\Controller {

    public function indexAction() {
        View::renderTemplate( 'Home/index.html' );
    }

    public function createAction() {
        $user = User::authenticate( $_POST['email'], $_POST['password'] );

        $remember_me = isset( $_POST['remember_me'] );

        if ( $user ) {

            Auth::login( $user, $remember_me );

            Flash::addMessage( 'Login successful' );

            $this->redirect( Auth::getReturnToPage() );

        } else {

            View::renderTemplate( 'Login/index.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me
            ] );
        }
    }

    public function destroyAction() {
        Auth::logout();

        $this->redirect( '/' );
    }
}
