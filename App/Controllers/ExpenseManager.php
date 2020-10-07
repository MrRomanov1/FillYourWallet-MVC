<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class ExpenseManager extends Authenticated {

    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();        
        
    }
    
    public function viewPageAction()
    {
        View::renderTemplate('Main/expense.html', [
            'user' => $this->user
        ]);
    }
}
