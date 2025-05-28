<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\UnitNotifications;
use App\Models\Address;
use App\Models\Building;
use App\Models\BuildingUnit;
use App\Models\DropdownType;
use App\Models\ManagerBuilding;
use App\Models\StaffMember;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserBuildingUnit;
use App\Models\UserUnitPicture;
use App\Notifications\CredentialsEmail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Customer;
use Stripe\Stripe;

class PropertyUsersController extends Controller
{
    public function index(Request $request)
    {
        try {
            $users = collect();
            $buildings = collect();
            $units = collect();
            $types = ['Rented', 'Sold'];

            $loggedUser = $request->user();
            $token = $request->attributes->get('token');

            $organizationId = $token['organization_id'] ?? null;
            $roleName = $token['role_name'] ?? null;

            $buildingId = $request->input('building_id');
            $unitId = $request->input('unit_id');
            $type = $request->input('type');
            $search = $request->input('search');

            $onlyBuildingIds = [];
            if ($roleName === 'Manager') {
                $onlyBuildingIds = ManagerBuilding::where('user_id', $loggedUser->id)->pluck('building_id')->toArray();

                if (empty($onlyBuildingIds)) {
                    return view('Heights.Owner.PropertyUsers.index', compact('users', 'buildings', 'units', 'types'));
                }
            }
            elseif ($roleName === 'Staff'){
                $staffRecord = StaffMember::where('user_id', $loggedUser->id)->first();
                $onlyBuildingIds = [$staffRecord->building_id];
            }

            $buildings = $this->getFilteredBuildings($organizationId, $onlyBuildingIds);
            $units = $this->getFilteredUnits($organizationId, $onlyBuildingIds);

            $users = User::with([
                'userUnits' => function ($query) use ($organizationId, $buildingId, $unitId, $type, $onlyBuildingIds) {
                    $query->where('organization_id', $organizationId)
                        ->where('contract_status', 1)
                        ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                        ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
                        ->when($type, fn($q) => $q->where('type', $type))
                        ->when(!empty($onlyBuildingIds), fn($q) => $q->whereIn('building_id', $onlyBuildingIds));
                }
            ])
                ->withCount([
                    'userUnits as rented_units_count' => function ($query) use ($organizationId, $buildingId, $unitId, $onlyBuildingIds) {
                        $query->where('type', 'rented')
                            ->where('organization_id', $organizationId)
                            ->where('contract_status', 1)
                            ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                            ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
                            ->when(!empty($onlyBuildingIds), fn($q) => $q->whereIn('building_id', $onlyBuildingIds));
                    },
                    'userUnits as sold_units_count' => function ($query) use ($organizationId, $buildingId, $unitId, $onlyBuildingIds) {
                        $query->where('type', 'sold')
                            ->where('organization_id', $organizationId)
                            ->where('contract_status', 1)
                            ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                            ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
                            ->when(!empty($onlyBuildingIds), fn($q) => $q->whereIn('building_id', $onlyBuildingIds));
                    }
                ])
                ->whereHas('userUnits', function ($query) use ($organizationId, $buildingId, $unitId, $type, $onlyBuildingIds) {
                    $query->where('organization_id', $organizationId)
                        ->where('contract_status', 1)
                        ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                        ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
                        ->when($type, fn($q) => $q->where('type', $type))
                        ->when(!empty($onlyBuildingIds), fn($q) => $q->whereIn('building_id', $onlyBuildingIds));
                })
                ->when($search, fn($q) => $q->where(function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                }))
                ->paginate(12);

            return view('Heights.Owner.PropertyUsers.index', compact('users', 'buildings', 'units', 'types'));

        } catch (\Throwable $e) {
            Log::error('Error in Property Users index: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $loggedUser = $request->user();
            $token = $request->attributes->get('token');
            $organizationId = $token['organization_id'];
            $roleName = $token['role_name'] ?? null;

            $unitId = $request->input('unit_id');
            $buildingId = $request->input('building_id');
            $type = $request->input('type');

            $onlyBuildingIds = [];

            if ($roleName === 'Manager') {
                $onlyBuildingIds = ManagerBuilding::where('user_id', $loggedUser->id)->pluck('building_id')->toArray();

                if (empty($onlyBuildingIds)) {
                    return back()->with('error', 'You do not have access to any buildings.');
                }
            }
            elseif ($roleName === 'Staff'){
                $staffRecord = StaffMember::where('user_id', $loggedUser->id)->first();
                $onlyBuildingIds = [$staffRecord->building_id];
            }

            $buildings = $this->getFilteredBuildings($organizationId, $onlyBuildingIds);
            $units = $this->getFilteredUnits($organizationId, $onlyBuildingIds);

            $user = User::where('id', $id)
                ->whereHas('userUnits', function ($query) use ($organizationId, $onlyBuildingIds) {
                    $query->where('organization_id', $organizationId)
                        ->where('contract_status', 1)
                        ->when(!empty($onlyBuildingIds), fn($q) => $q->whereIn('building_id', $onlyBuildingIds));
                })
                ->first();

            if (!$user) {
                return back()->with('error', 'The user could not be found or has no active rented or sold units associated with your organization.');
            }

            $userUnits = $user->userUnits()
                ->where('organization_id', $organizationId)
                ->where('contract_status', 1)
                ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
                ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                ->when($type, fn($q) => $q->where('type', $type))
                ->when(!empty($onlyBuildingIds), fn($q) => $q->whereIn('building_id', $onlyBuildingIds))
                ->with(['building', 'unit'])
                ->paginate(12);

            $types = ['Rented', 'Sold'];
            return view('Heights.Owner.PropertyUsers.show', compact('user', 'userUnits', 'buildings', 'units', 'types'));

        } catch (\Throwable $e) {
            Log::error("Error in Property Users show: " . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    public function Discontinue(Request $request){

    }


    //Helper functions
    private function getFilteredBuildings($organizationId, $onlyBuildingIds = [])
    {
        return Building::where('organization_id', $organizationId)
            ->whereIn('status', ['Approved', 'For Re-Approval'])
            ->where('isFreeze', 0)
            ->when(!empty($onlyBuildingIds), fn($q) => $q->whereIn('id', $onlyBuildingIds))
            ->select('id', 'name')
            ->orderBy('name', 'ASC')
            ->get();
    }

    private function getFilteredUnits($organizationId, $onlyBuildingIds = [])
    {
        return BuildingUnit::where('organization_id', $organizationId)
            ->where('status', 'Approved')
            ->where('availability_status', '!=', 'Available')
            ->when(!empty($onlyBuildingIds), fn($q) => $q->whereIn('building_id', $onlyBuildingIds))
            ->select('id', 'unit_name')
            ->orderBy('unit_name', 'ASC')
            ->get();
    }

}
