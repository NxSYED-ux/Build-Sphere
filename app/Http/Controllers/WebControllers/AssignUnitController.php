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

class AssignUnitController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user() ?? abort(403, 'Unauthorized');
            $token = $request->attributes->get('token');

            $selectedBuildingId = null;
            $selectedUnitId = $request->input('unit_id');
            $selectedUserId = $request->input('user_id');

            $dropdownData = DropdownType::with(['values.childs.childs'])
                ->where('type_name', 'Country')
                ->get();

            $users = User::where('id', '!=', $user->id)->pluck('name', 'id');
            $buildings = collect();

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return view('Heights.Owner.AssignUnits.index', compact('selectedBuildingId', 'selectedUnitId', 'selectedUserId', 'users', 'buildings', 'dropdownData'));
            }

            $organizationId = $token['organization_id'];
            $roleName = $token['role_name'];

            $query = Building::where('organization_id', $organizationId)->whereNotIn('status', ['Rejected', 'Under Processing']);

            if ($roleName === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
                $query->whereIn('id', $managerBuildingIds);

            }elseif ($roleName === 'Staff'){
                $staffRecord = StaffMember::where('user_id', $user->id)->first();
                if ($staffRecord) {
                    $selectedBuildingId = $staffRecord->building_id;
                    $query->where('id', $selectedBuildingId);
                }
            }

            $buildings = $query->get();

            if($selectedUnitId){
                $checkingSelectedUnit = BuildingUnit::where('id', $selectedUnitId)
                    ->where('sale_or_rent', '!=', 'Not Available')
                    ->where('availability_status', 'Available')
                    ->where('status', 'Approved')
                    ->first();
                if(!$checkingSelectedUnit || $checkingSelectedUnit->organization_id != $organizationId){
                    return redirect()->back()->with('error', 'Invalid Unit ID');
                }

                $selectedBuildingId = $checkingSelectedUnit?->building_id;
            }

            return view('Heights.Owner.AssignUnits.index', compact('selectedBuildingId', 'selectedUnitId', 'selectedUserId', 'users', 'buildings', 'dropdownData'));
        } catch (\Exception $e) {
            Log::error("Error in AssignUnits index: {$e->getMessage()}", ['exception' => $e]);
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function create(Request $request)
    {
        $loggedUser = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        if (empty($token['organization_id']) || empty($token['role_name'])) {
            return redirect()->back()->with('error', 'Organization Id is missing');
        }

        $organizationId = $token['organization_id'];
        $roleName = $token['role_name'];

        $request->validate([
            'userId' => ['nullable', 'integer', 'exists:users,id'],

            'user_name' => ['required_without:userId', 'string', 'max:255'],
            'user_email' => ['required_without:userId', 'string', 'email', 'max:255', 'unique:users,email'],
            'user_phone_no' => ['required_without:userId', 'string', 'max:20'],
            'user_cnic' => ['required_without:userId', 'max:18', 'unique:users,cnic'],
            'user_picture' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:5120'],
            'user_gender' => ['required_without:userId', 'in:Male,Female,Other'],
            'user_date_of_birth' => ['required_without:userId', 'date'],
            'user_country' => ['required_without:userId', 'string', 'max:50'],
            'user_province' => ['required_without:userId', 'string', 'max:50'],
            'user_city' => ['required_without:userId', 'string', 'max:50'],
            'user_location' => ['required_without:userId', 'string', 'max:255'],
            'user_postal_code' => ['required_without:userId', 'string', 'max:50'],

            'unitId' => ['required', 'integer', 'exists:buildingunits,id'],
            'buildingId' => ['required', 'integer', 'exists:buildings,id'],
            'type' => ['required', 'in:Rented,Sold'],
            'price' => ['required', 'numeric', 'min:0'],
            'no_of_months' => ['nullable', 'integer', 'min:1', 'required_if:type,Rented'],
            'pictures.*' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        DB::beginTransaction();

        try {
            if ($roleName === 'Manager' && !ManagerBuilding::where('building_id', $request->buildingId)
                    ->where('user_id', $loggedUser->id)
                    ->exists()) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'You do not have access to assign units of the selected building.');
            }
            elseif ($roleName === 'Staff'){
                $staffRecord = StaffMember::where('user_id', $loggedUser->id)->first();

                if (!$staffRecord || $staffRecord->building_id != $request->buildingId) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'You do not have access to assign units of the selected building.');
                }
            }

            try {
                $user = $request->userId ? User::findOrFail($request->userId) : $this->createUser($request);
            } catch (ModelNotFoundException $e) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'User not found.');
            }

            $existingUnit = UserBuildingUnit::where([
                ['unit_id', '=', $request->unitId],
                ['contract_status', '=', 1]
            ])->exists();

            if ($existingUnit) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'This unit is already assigned to a user, Please choose another unit.');
            }

            $unit = BuildingUnit::find($request->unitId);

            [$assignedUnit, $transaction] = $this->unitAssignment_Transaction($user, $unit, $request->type,null, $request->price, (int) $request->no_of_months);

            if ($request->hasFile('pictures')) {
                foreach ($request->file('pictures') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = 'uploads/units/contract/' . $imageName;
                    $image->move(public_path('uploads/units/contract'), $imageName);

                    UserUnitPicture::create([
                        'user_unit_id' => $assignedUnit->id,
                        'file_path' => $imagePath,
                        'file_name' => $imageName,
                    ]);
                }
            }

            DB::commit();

            $userHeading = "{$unit->unit_name} " . ($request->type === 'Sold' ? 'Purchased' : 'Rented') . " Successfully!";
            $userMessage = "Congratulations! You have successfully " .
                ($request->type === 'Sold' ? 'purchased' : 'rented') .
                " {$unit->unit_name} for the price of {$request->price} PKR" .
                ($request->type === 'Sold' ? '.' : " per {$request->no_of_months} month.");


            dispatch( new UnitNotifications(
                $organizationId,
                $unit->id,
                "{$unit->unit_name} Assigned Successfully by {$roleName}",
                "{$unit->unit_name} has been {$request->type} successfully for Price: {$request->price} ",
                "owner/units/{$unit->id}/show",

                $loggedUser->id,
                "{$unit->unit_name} Assigned Successfully",
                "{$unit->unit_name} has been {$request->type} successfully for Price: {$request->price} ",
                "owner/units/{$unit->id}/show",

                $request->userId ?? $user->id,
                $userHeading,
                $userMessage,
                "",
            ));


            dispatch(new UnitNotifications(
                $organizationId,
                $unit->id,
                "Transaction Completed Successfully by {$roleName}",
                "A payment of {$request->price} PKR has been successfully recorded for your {$request->type} of {$unit->unit_name}.",
                "owner/finance/{$transaction->id}/show",

                $loggedUser->id,
                "Transaction Completed Successfully",
                "A payment of {$request->price} PKR has been recorded for {$unit->unit_name}.",
                "owner/finance/{$transaction->id}/show",

                $request->userId ?? $user->id,
                "Transaction Successful!",
                "You have successfully made a payment of {$request->price} PKR for {$unit->unit_name}.",
                "",
            ));

            return redirect()->back()->with('success', 'Unit assigned successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in unit assignment : ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    private function createUser($request)
    {
        $password = Str::random(8);

        if ($request->hasFile('user_picture')) {
            $profileImage = $request->file('user_picture');
            $profileImageName = time() . '_' . $profileImage->getClientOriginalName();
            $profileImagePath = 'uploads/users/images/' . $profileImageName;
            $profileImage->move(public_path('uploads/users/images'), $profileImageName);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $stripeCustomer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone_no,
            'address' => [
                'line1' => $request->location,
                'city' => $request->city,
                'state' => $request->province,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
            ],
        ]);

        $address = Address::create([
            'location' => $request->user_location,
            'country' => $request->user_country,
            'province' => $request->user_province,
            'city' => $request->user_city,
            'postal_code' => $request->user_postal_code,
        ]);

        $user = User::create([
            'name' => $request->user_name,
            'email' => $request->user_email,
            'password' => Hash::make($password),
            'phone_no' => $request->user_phone_no,
            'cnic' => $request->user_cnic,
            'picture' => $profileImagePath ?? null,
            'gender' => $request->user_gender,
            'role_id' => 5,
            'address_id' => $address->id,
            'date_of_birth' => $request->user_date_of_birth,
            'customer_payment_id' => $stripeCustomer->id,
        ]);

        $user->notify( new CredentialsEmail(
            $user->name,
            $user->email,
            $password,
        ));

        return $user;
    }


    private function unitAssignment_Transaction($user, $unit, $type, $paymentIntentId, $price, int $billing_cycle = 1, $currency = 'PKR')
    {
        $unit->update(['availability_status' => $type]);

        $assignedUnit = UserBuildingUnit::create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
            'building_id' => $unit->building_id,
            'organization_id' => $unit->organization_id,
            'type' => $type,
            'price' => $price,
            'billing_cycle' => $billing_cycle
        ]);

        $source_id = $assignedUnit->id;
        $source_name = 'unit contract';

        if ($type === 'Rented') {
            $subscription = Subscription::create([
                'customer_payment_id' => $user->customer_payment_id,
                'building_id' => $unit->building_id,
                'unit_id' => $unit->id,
                'user_id' => $user->id,
                'organization_id' => $unit->organization_id,
                'source_id' => $source_id,
                'source_name' => $source_name,
                'billing_cycle' => $billing_cycle,
                'subscription_status' => 'Active',
                'price_at_subscription' => $assignedUnit->price,
                'currency_at_subscription' => $currency,
                'ends_at' => now()->addMonths($billing_cycle),
            ]);

            $source_id = $subscription->id;
            $source_name = 'subscription';

            $assignedUnit->update(['subscription_id' => $subscription->id]);
        }

        $transaction = Transaction::create([
            'transaction_title' => $unit->unit_name,
            'transaction_category' => 'New',
            'building_id' => $unit->building_id,
            'unit_id' => $unit->id,
            'buyer_id' => $user->id,
            'buyer_type' => 'user',
            'seller_type' => 'organization',
            'seller_id' => $unit->organization_id,
            'payment_method' => 'Cash',
            'gateway_payment_id' => $paymentIntentId,
            'price' => $assignedUnit->price,
            'currency' => $currency,
            'status' => 'Completed',
            'is_subscription' => $type === 'Rented',
            'billing_cycle' => $type === 'Rented' ? "{$billing_cycle} Month" : null,
            'subscription_start_date' => $type === 'Rented' ? now() : null,
            'subscription_end_date' => $type === 'Rented' ? now()->addMonths($billing_cycle) : null,
            'source_id' => $source_id,
            'source_name' => $source_name,
        ]);

        return [$assignedUnit, $transaction];
    }

}
