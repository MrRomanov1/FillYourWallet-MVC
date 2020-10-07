<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Balance extends Authenticated {

    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();        
        
    }
    
    public function currentMonthBalanceAction() {
        $this->viewPage();
    }
    
    public function currentYearBalanceAction() {
        $this->viewPage();
    }
    
    public function lastMonthBalanceAction() {
        $this->viewPage();
    }
    
    public function viewPage()
    {
        View::renderTemplate('Main/balance.html', [
            'user' => $this->user
        ]);
    }
}
