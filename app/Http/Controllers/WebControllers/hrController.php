<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\ManagerBuilding;
use App\Models\StaffMember;
use Illuminate\Http\Request;


class hrController extends Controller
{
    public function staffIndex(){

    }

    public function ManagerIndex(){

    }

    private function index(string $roleId, Request $request)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');
        $token = $request->attributes->get('token');

        if (!$token || empty($token['organization_id']) || empty($token['role_name'])) {
            return redirect()->back()->with('error', 'This info is for Organization related personals');
        }

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];

        $selectedDepartment = $request->input('DepartmentId');

        $staffQuery = StaffMember::where('organization_id', $organization_id)
            ->whereHas('user', function ($query) use ($roleId) {
                $query->where('role_id', $roleId);
            })
            ->when($selectedDepartment, function ($query) use ($selectedDepartment) {
                $query->where('department_id', $selectedDepartment);
            })
            ->with('user');


        if ($role_name === 'Manager') {
            $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
            $staffQuery->whereIn('building_id', $managerBuildingIds);
        }

        $staffMembers = $staffQuery->paginate(10);

        $departments = Department::where();

        return $staffMembers;
    }


}
