<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Organization;
use App\Models\User;

class AdminDashboardController extends Controller
{
    //
    public function index(){

        return view('Heights.Admin.Dashboard.admin_dashboard');

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
