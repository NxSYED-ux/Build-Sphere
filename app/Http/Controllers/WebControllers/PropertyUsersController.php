<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\UnitNotifications;
use App\Models\Address;
use App\Models\Building;
use App\Models\BuildingUnit;
use App\Models\DropdownType;
use App\Models\ManagerBuilding;
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
        $loggedUser = $request->user() ?? abort(403, 'Unauthorised Access');

        try {
            $users = collect();
            $buildings = collect();
            $units = collect();
            $types = ['Rented', 'Sold'];

            $token = $request->attributes->get('token');

            $organizationId = $token['organization_id'] ?? null;
            $roleName = $token['role_name'] ?? null;

            if (!$organizationId || !$roleName) {
                return view('Heights.Owner.PropertyUsers.index', compact('users', 'buildings', 'units', 'types'));
            }

            $buildingId = $request->input('building_id');
            $unitId = $request->input('unit_id');
            $type = $request->input('type');
            $search = $request->input('search');

            $managerBuildingIds = [];

            if ($roleName === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $loggedUser->id)->pluck('building_id')->toArray();

                if (empty($managerBuildingIds)) {
                    return view('Heights.Owner.PropertyUsers.index', compact('users', 'buildings', 'units', 'types'));
                }
            }

            $buildings = Building::where('organization_id', $organizationId)
                ->when(!empty($managerBuildingIds), fn($q) => $q->whereIn('id', $managerBuildingIds))
                ->get();

            $units = BuildingUnit::where('organization_id', $organizationId)
                ->when(!empty($managerBuildingIds), fn($q) => $q->whereIn('building_id', $managerBuildingIds))
                ->get();

            $users = User::with([
                'userUnits' => function ($query) use ($organizationId, $buildingId, $unitId, $type, $managerBuildingIds) {
                    $query->where('organization_id', $organizationId)
                        ->where('contract_status', 1)
                        ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                        ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
                        ->when($type, fn($q) => $q->where('type', $type))
                        ->when(!empty($managerBuildingIds), fn($q) => $q->whereIn('building_id', $managerBuildingIds));
                }
            ])
                ->withCount([
                    'userUnits as rented_units_count' => function ($query) use ($organizationId, $buildingId, $unitId, $managerBuildingIds) {
                        $query->where('type', 'rented')
                            ->where('organization_id', $organizationId)
                            ->where('contract_status', 1)
                            ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                            ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
                            ->when(!empty($managerBuildingIds), fn($q) => $q->whereIn('building_id', $managerBuildingIds));
                    },
                    'userUnits as sold_units_count' => function ($query) use ($organizationId, $buildingId, $unitId, $managerBuildingIds) {
                        $query->where('type', 'sold')
                            ->where('organization_id', $organizationId)
                            ->where('contract_status', 1)
                            ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                            ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
                            ->when(!empty($managerBuildingIds), fn($q) => $q->whereIn('building_id', $managerBuildingIds));
                    }
                ])
                ->whereHas('userUnits', function ($query) use ($organizationId, $buildingId, $unitId, $type, $managerBuildingIds) {
                    $query->where('organization_id', $organizationId)
                        ->where('contract_status', 1)
                        ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                        ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
                        ->when($type, fn($q) => $q->where('type', $type))
                        ->when(!empty($managerBuildingIds), fn($q) => $q->whereIn('building_id', $managerBuildingIds));
                })
                ->when($search, fn($q) => $q->where(function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                }))
                ->paginate(12);

            return view('Heights.Owner.PropertyUsers.index', compact('users', 'buildings', 'units', 'types'));

        } catch (\Exception $e) {
            Log::error('Error in Property Users index: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function show(Request $request, $id)
    {
        $loggedUser = $request->user() ?? abort(403, 'Unauthorised Access');
        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id'])) {
                return back()->with('error', 'This information is restricted to authorized organization personnel only.');
            }

            $organizationId = $token['organization_id'];
            $roleName = $token['role_name'] ?? null;

            $unitId = $request->input('unit_id');
            $buildingId = $request->input('building_id');

            $managerBuildingIds = [];

            if ($roleName === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $loggedUser->id)->pluck('building_id')->toArray();

                if (empty($managerBuildingIds)) {
                    return back()->with('error', 'You do not have access to any buildings.');
                }
            }

            $buildings = Building::where('organization_id', $organizationId)
                ->when(!empty($managerBuildingIds), fn($q) => $q->whereIn('id', $managerBuildingIds))
                ->get();

            $units = BuildingUnit::where('organization_id', $organizationId)
                ->when(!empty($managerBuildingIds), fn($q) => $q->whereIn('building_id', $managerBuildingIds))
                ->get();

            $user = User::where('id', $id)
                ->whereHas('userUnits', function ($query) use ($organizationId, $managerBuildingIds) {
                    $query->where('organization_id', $organizationId)
                        ->where('contract_status', 1)
                        ->when(!empty($managerBuildingIds), fn($q) => $q->whereIn('building_id', $managerBuildingIds));
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
                ->when(!empty($managerBuildingIds), fn($q) => $q->whereIn('building_id', $managerBuildingIds))
                ->with(['building', 'unit'])
                ->paginate(12);

            $types = ['Rented', 'Sold'];
            return view('Heights.Owner.PropertyUsers.show', compact('user', 'userUnits', 'buildings', 'units', 'types'));

        } catch (\Exception $e) {
            Log::error("Error in Property Users show: " . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }



    public function Discontinue(Request $request){

    }

}
