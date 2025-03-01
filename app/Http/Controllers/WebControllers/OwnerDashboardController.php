<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;

class OwnerDashboardController extends Controller
{
    //
    public function index(){

        return view('Heights.Owner.Dashboard.owner_dashboard');

    }
}
