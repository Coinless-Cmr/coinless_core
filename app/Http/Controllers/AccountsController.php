<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Models\Accounts as AccountsModel;

class Accounts extends Controller {

    /**
     * Index of the controller.
     */
    public function index(){
        $accountsModel  = new Cor_accounts();
        return $accountsModel->getAll();
    }

    /**
     * Insert new subscription.
     */
    public function subscribe(Request $request) {
        $accountsModel  = new Cor_accounts();
        return $accountsModel->subscribe(['userid' => 'ZhezxTPAo1ikLF5dXS6CMQ3D8Uj9vaGl' ]);
    }

    /**
     * Get current subscription of user.
     */
    public function current(Request $request) {
        $accountsModel  = new Cor_accounts();
        return $accountsModel->getCurrent('ZhezxTPAo1ikLF5dXS6CMQ3D8Uj9vaGl');
    }
}
