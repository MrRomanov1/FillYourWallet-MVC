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
            $errors['nameErrors'] = $user -> nameErrors;
            $errors['emailErrors'] = $user -> emailErrors;
            $errors['passwordErrors'] = $user -> passwordErrors;
            
            View::renderTemplate( 'Signup/new.html', [
                'user' => $user,
                'errors' => $errors
            ] );
        } else {
            $user->saveUserData();
            $_SESSION['success'] = true;
            $this->redirect( '' );
        }

    }

}
