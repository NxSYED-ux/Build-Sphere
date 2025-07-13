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
use App\Services\AssignUnitService;
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
                ->whereIn('building_id', $buildingIds)
                ->with(['building', 'unit'])
                ->get();

            return view('Heights.Owner.PropertyUsers.show', compact('user', 'userUnits', 'buildings', 'units', 'types'));

        } catch (\Throwable $e) {
            Log::error("Error in Property Users show: " . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function updateRenewStatus(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:userbuildingunits,id',
            'value' => 'required|in:0,1',
        ], [
            'contract_id.required' => 'The contract ID is required.',
            'contract_id.exists' => 'The selected contract does not exist.',
            'value.required' => 'The contract status value is required.',
            'value.in' => 'The contract status must be either 0 (Discontinue) or 1 (Continue).',
        ]);

        try {
            $requestedAction = (int) $request->value === 1 ? 'discontinued' : 'continued';

            $contract = UserBuildingUnit::where('id', $request->contract_id)
                ->where('contract_status', 1)
                ->first();

            if (!$contract) {
                return response()->json([
                    'error' => 'The specified contract is either inactive or does not exist.'
                ], 404);
            }

            if ($contract->type === 'Sold') {
                return response()->json([
                    'error' => 'This contract cannot be modified because the unit has already been sold.'
                ], 404);
            }

            if ((int)$contract->renew_canceled === (int)$request->value) {
                return response()->json([
                    'error' => "The contract is already marked as $requestedAction."
                ], 400);
            }

            $contract->renew_canceled = $request->value;
            $contract->save();

            return response()->json([
                'message' => "The contract has been successfully $requestedAction."
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error while updating contract status: ' . $e->getMessage());

            return response()->json([
                'error' => 'An unexpected error occurred while updating the contract. Please try again later.'
            ], 500);
        }
    }

    public function updateContract(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:userbuildingunits,id',
            'billing_cycle' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        try {
            $authenticatedUserId = $request->user()->id;
            $contract = UserBuildingUnit::with('user')
                ->where('id', $request->contract_id)
                ->where('contract_status', 1)
                ->first();

            $contractUser = $contract?->user;

            if (!$contract) {
                return redirect()->back()->with('error', 'The contract you are trying to update is not available or has been deactivated.');
            }

            if ($authenticatedUserId === $contractUser->id) {
                return redirect()->back()->with('error', "You cannot update your own contract due to business policy.");
            }

            if ($contract->type === 'Sold') {
                return redirect()->back()->with('error', 'You cannot edit this contract because the unit is sold.');
            }

            $contract->update([
                'billing_cycle' => $request->billing_cycle,
                'price' => $request->price,

            ]);

            return redirect()->back()->with('success', 'Contract updated successfully.');

        } catch (\Throwable $e) {
            Log::error('Error editing contract: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An unexpected error occurred while updating the contract.');
        }
    }

    public function editContract($id)
    {
        try {
            $authenticatedUserId = request()->user()->id;

            $contract = UserBuildingUnit::with('user')
                ->where('id', $id)
                ->where('contract_status', 1)
                ->first();

            if (!$contract) {
                return response()->json([
                    'error' => 'The contract you are trying to edit is not available or has been deactivated.'
                ], 404);
            }

            if ($authenticatedUserId === $contract->user->id) {
                return response()->json([
                    'error' => 'You cannot edit your own contract due to business policy.'
                ], 403);
            }

            return response()->json([
                'contract' => $contract
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error editing contract: ' . $e->getMessage());

            return response()->json([
                'error' => 'An unexpected error occurred while retrieving the contract.'
            ], 500);
        }
    }

    public function markAsPaymentReceived(Request $request)
    {
        $request->validate([
            'user_unit_id' => 'required|exists:userbuildingunits,id',
        ]);

        $loggedUser = request()->user();

        DB::beginTransaction();

        try {
            $currentUnitContract = UserBuildingUnit::with('unit', 'user')->findOrFail($request->user_unit_id);

            if ($currentUnitContract->type === 'Sold') {
                return response()->json([
                    'message' => 'This unit is already marked as Sold. Payment cannot be processed.'
                ], 422);
            }

            $user = $currentUnitContract->user;
            $unit = $currentUnitContract->unit;

            $existingSubscription = Subscription::find($currentUnitContract->subscription_id);

            $assignmentUnitService = new AssignUnitService();
            [$assignedUnit, $transaction] = $assignmentUnitService->unitAssignment_Transaction(
                $user,
                $unit,
                $currentUnitContract->type,
                null,
                $currentUnitContract->price,
                $currentUnitContract->billing_cycle,
                'Cash',
                'PKR',
                'renewal',
                $currentUnitContract,
                $existingSubscription,
            );

            $assignmentUnitService->sendUnitAssignmentNotifications($unit, $transaction, $user->id, $assignedUnit, $loggedUser);

            DB::commit();

            return response()->json([
                'message' => 'Payment marked as received and transaction recorded.',
                'transaction' => $transaction,
            ]);

        } catch (\Throwable $e) {
            DB::rollback();
            Log::error('Rental payment failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to mark payment as received.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
