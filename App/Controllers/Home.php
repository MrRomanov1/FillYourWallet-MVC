<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\User;

class Home extends \Core\Controller {    
    

    public function indexAction() {
        if (Auth::getUser() ) {
            $this->redirect( '/main' );
        }
        else {
            View::renderTemplate( 'Home/index.html' );
        }
        
    }

    public function createAction() {
        $user = User::authenticateUser( $_POST['email'], $_POST['password'] );
        
        if ( $user ) {

            Auth::login( $user );

            $this->redirect( Auth::getReturnToPage() );            

        } else {
            
            View::renderTemplate( 'Home/index.html', [
                'email' => $_POST['email']
            ] );
        }
    }

    public function destroyAction() {
        Auth::logout();
        $this->redirect( '' );

        //$this->redirect( '/login/show-logout-message' );
    }

    public function showLogoutMessageAction() {

        //$this->redirect( '/' );
    }
}
