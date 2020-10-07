<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class IncomeManager extends Authenticated {

    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();        
        
    }
    
    public function viewPageAction()
    {
        View::renderTemplate('Main/income.html', [
            'user' => $this->user
        ]);
    }
}
