<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OwnerDashboardController extends Controller
{
    //
    public function index(){  

        return view('Owner/owner_dashboard');

    } 
}
