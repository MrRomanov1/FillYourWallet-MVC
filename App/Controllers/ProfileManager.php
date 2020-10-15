<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;

class ProfileManager extends Authenticated {

    protected function before() {
        parent::before();

        $this->user = Auth::getUser();

    }

    public function configAction() {
        View::renderTemplate( 'Profile/config.html', [

            'user' => $this->user
        ] );
    }

}
