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

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return view('Heights.Owner.PropertyUsers.index', compact('users'));
            }

            $organizationId = $token['organization_id'];

            $users = User::with([
                'userUnits' => fn($q) => $q->where('organization_id', $organizationId)
            ])
                ->withCount([
                    'userUnits as rented_units_count' => fn($q) => $q->where('type', 'rented')->where('organization_id', $organizationId),
                    'userUnits as sold_units_count' => fn($q) => $q->where('type', 'sold')->where('organization_id', $organizationId)
                ])
                ->whereHas('userUnits', fn($q) => $q->where('organization_id', $organizationId))
                ->paginate(12);

            return view('Heights.Owner.PropertyUsers.index', compact('users'));

        } catch (\Exception $e) {
            Log::error('Error in Property Users index: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

}
