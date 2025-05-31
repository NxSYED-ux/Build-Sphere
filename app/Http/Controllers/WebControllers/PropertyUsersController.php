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
use App\Services\OwnerFiltersService;
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
        $search = $request->input('search');
        $buildingId = $request->input('building_id');
        $unitId = $request->input('unit_id');
        $type = $request->input('type');

        try {
            $token = $request->attributes->get('token');
            $organizationId = $token['organization_id'];

            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();
            $buildings = $ownerService->approvedBuildings($buildingIds);
            $units = $ownerService->rentedOrSoldUnits($buildingIds);
            $types = ['Rented', 'Sold'];

            $users = User::with([
                'userUnits' => function ($query) use ($organizationId, $buildingId, $unitId, $type, $buildingIds) {
                    $query->where('organization_id', $organizationId)
                        ->whereIn('building_id', $buildingIds)
                        ->where('contract_status', 1)
                        ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                        ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
                        ->when($type, fn($q) => $q->where('type', $type));
                }
            ])
                ->withCount([
                    'userUnits as rented_units_count' => function ($query) use ($organizationId, $buildingId, $unitId, $buildingIds) {
                        $query->where('type', 'rented')
                            ->where('organization_id', $organizationId)
                            ->whereIn('building_id', $buildingIds)
                            ->where('contract_status', 1)
                            ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                            ->when($unitId, fn($q) => $q->where('unit_id', $unitId));
                    },
                    'userUnits as sold_units_count' => function ($query) use ($organizationId, $buildingId, $unitId, $buildingIds) {
                        $query->where('type', 'sold')
                            ->where('organization_id', $organizationId)
                            ->whereIn('building_id', $buildingIds)
                            ->where('contract_status', 1)
                            ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                            ->when($unitId, fn($q) => $q->where('unit_id', $unitId));
                    }
                ])
                ->whereHas('userUnits', function ($query) use ($organizationId, $buildingId, $unitId, $type, $buildingIds) {
                    $query->where('organization_id', $organizationId)
                        ->whereIn('building_id', $buildingIds)
                        ->where('contract_status', 1)
                        ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                        ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
                        ->when($type, fn($q) => $q->where('type', $type));
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
        $unitId = $request->input('unit_id');
        $buildingId = $request->input('building_id');
        $type = $request->input('type');

        try {
            $token = $request->attributes->get('token');
            $organizationId = $token['organization_id'];

            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();
            $buildings = $ownerService->approvedBuildings($buildingIds);
            $units = $ownerService->rentedOrSoldUnits($buildingIds);
            $types = ['Rented', 'Sold'];

            $user = User::where('id', $id)
                ->whereHas('userUnits', function ($query) use ($organizationId, $buildingIds) {
                    $query->where('organization_id', $organizationId)
                        ->where('contract_status', 1)
                        ->whereIn('building_id', $buildingIds);
                })
                ->first();

            if (!$user) {
                return back()->with('error', 'The user could not be found or has no active rented or sold units associated with your organization or buildings.');
            }

            $userUnits = $user->userUnits()
                ->where('organization_id', $organizationId)
                ->where('contract_status', 1)
                ->when($unitId, fn($q) => $q->where('unit_id', $unitId))
                ->when($buildingId, fn($q) => $q->where('building_id', $buildingId))
                ->when($type, fn($q) => $q->where('type', $type))
                ->whereIn('building_id', $buildingIds)
                ->with(['building', 'unit'])
                ->paginate(12);

            return view('Heights.Owner.PropertyUsers.show', compact('user', 'userUnits', 'buildings', 'units', 'types'));

        } catch (\Throwable $e) {
            Log::error("Error in Property Users show: " . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    public function Discontinue(Request $request){

    }

}
