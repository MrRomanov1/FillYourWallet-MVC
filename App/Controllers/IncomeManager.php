<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;

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
    
    public function addIncomeAction() {		
		$income = new Income($_POST);
		
		if ($income->saveUserIncome($this->user->userId)) {
            echo 'dodane';
			;
		} else {
            echo 'niedodane';
			;
		}
	}
    
}
