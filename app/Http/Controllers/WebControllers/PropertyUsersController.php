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
        try {
            $users = collect();

            $token = $request->attributes->get('token');

            if (empty($token['organization_id'])) {
                return view('Heights.Owner.PropertyUsers.index', compact('users'));
            }

            $organizationId = $token['organization_id'];

            $users = User::with([
                'userUnits' => fn ($query) =>
                $query->where('organization_id', $organizationId)
                    ->where('status', 1)
            ])
                ->withCount([
                    'userUnits as rented_units_count' => fn ($query) =>
                    $query->where('type', 'rented')
                        ->where('organization_id', $organizationId)
                        ->where('status', 1),

                    'userUnits as sold_units_count' => fn ($query) =>
                    $query->where('type', 'sold')
                        ->where('organization_id', $organizationId)
                        ->where('status', 1)
                ])
                ->whereHas('userUnits', fn ($query) =>
                $query->where('organization_id', $organizationId)
                    ->where('status', 1)
                )
                ->paginate(12);

            return view('Heights.Owner.PropertyUsers.index', compact('users'));

        } catch (\Exception $e) {
            Log::error('Error in Property Users index: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id'])) {
                return back()->with('error', 'This information is restricted to authorized organization personnel only.');
            }

            $organizationId = $token['organization_id'];

            $user = User::with([
                'userUnits' => fn($query) =>
                $query->where('organization_id', $organizationId)
                    ->where('status', 1)
                    ->with(['building', 'unit', 'subscription'])
            ])
                ->where('id', $id)
                ->whereHas('userUnits', fn($query) =>
                $query->where('organization_id', $organizationId)
                    ->where('status', 1)
                )
                ->first();

            if (!$user || $user->userUnits->isEmpty()) {
                return back()->with('error', 'The user could not be found or has no active rented or sold units associated with your organization.');
            }

            return view('Heights.Owner.PropertyUsers.show', compact('user'));

        } catch (\Exception $e) {
            Log::error("Error in Property Users show: " . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

}
