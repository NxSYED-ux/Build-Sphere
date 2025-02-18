<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Organization;
use App\Models\User;  

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    //
    public function index(){  

        return view('Admin/admin_dashboard');

    } 

    public function data()
    {
        $buildings = Building::count();
        $organizations = Organization::count();
        $owners = User::where('role_id',2)->count();
        $buildingsForApproval = Building::whereIn('status', ['Under Review', 'Reapproved'])->count();

        return response()->json([
            'counts' => [
                'buildings' => $buildings, 
                'organizations' => $organizations, 
                'owners' => $owners, 
                'buildingsForApproval' => $buildingsForApproval, 
            ], 
        ]);
    }
    
}
