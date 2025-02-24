<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OwnerDashboardController extends Controller
{
    //
    public function index(){

        return view('Heights.Owner.Dashboard.owner_dashboard');

    }
}
