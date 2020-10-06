<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Main extends Authenticated {

    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();        
        
    }
    
    public function indexAction()
    {
        View::renderTemplate('Main/index.html', [
            'user' => $this->user
        ]);
    }
}
