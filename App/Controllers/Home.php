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
            $success = false;
            if (isset($_SESSION['success'])) {
                $success = $_SESSION['success'];
                unset ($_SESSION['success']);
            }
            
            View::renderTemplate( 'Home/index.html', [
                'success' => $success                
            ] ); 
        }
        
    }

    public function createAction() {
        $user = User::authenticateUser( $_POST['email'], $_POST['password'] );
        
        if ( $user ) {
            $success = false;
            Auth::login( $user );

            $this->redirect( Auth::getReturnToPage() );            

        } else {
            $loginError = 'Podano niepoprawne dane logowania!';
            View::renderTemplate( 'Home/index.html', [
                'email' => $_POST['email'],
                'loginError' => $loginError
            ] );
        }
    }

    public function destroyAction() {
        Auth::logout();
        $this->redirect( '' );
        
    }   
}
