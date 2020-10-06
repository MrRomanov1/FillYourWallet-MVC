<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;

class Signup extends \Core\Controller {

    public function newAction() {
        if (Auth::getUser() ) {
            $this->redirect( '/main' );
        }
        else {
            View::renderTemplate( 'Signup/new.html' );
        }
        
    }

    public function createAction() {
        $user = new User( $_POST );

        if ( ! $user->saveUserModel() ) {

            View::renderTemplate( 'Signup/new.html', [
                'user' => $user
            ] );
        } else {
            $user->saveUserData();

            $this->redirect( '' );
        }

    }

}
