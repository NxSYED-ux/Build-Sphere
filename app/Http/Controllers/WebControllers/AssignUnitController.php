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

class AssignUnitController extends Controller
{
    public function index(Request $request)
    {
        try {
            $isOwner = $request->user()->id === 2;

            $token = $request->attributes->get('token');
            $organizationId = $token['organization_id'];

            $selectedBuildingId = null;
            $selectedUnitId = $request->input('unit_id');
            $selectedUserId = $request->input('user_id');

            $dropdownData = DropdownType::with(['values.childs.childs'])
                ->where('type_name', 'Country')
                ->get();

            $ownerService = new OwnerFiltersService();
            $users = $ownerService->users(!$isOwner);
            $buildingIds = $ownerService->getAccessibleBuildingIds();
            $buildings = $ownerService->approvedBuildings($buildingIds);

            if($selectedUnitId){
                $checkingSelectedUnit = BuildingUnit::where('id', $selectedUnitId)
                    ->where('sale_or_rent', '!=', 'Not Available')
                    ->where('availability_status', 'Available')
                    ->where('status', 'Approved')
                    ->whereIn('building_id', $buildingIds)
                    ->first();

                if(!$checkingSelectedUnit || $checkingSelectedUnit->organization_id != $organizationId){
                    return redirect()->back()->with('error', 'Invalid Unit ID');
                }

                $selectedBuildingId = $checkingSelectedUnit->building_id;
            }

            return view('Heights.Owner.AssignUnits.index', compact('selectedBuildingId', 'selectedUnitId', 'selectedUserId', 'users', 'buildings', 'dropdownData'));
        } catch (\Throwable $e) {
            Log::error("Error in AssignUnits index: {$e->getMessage()}", ['exception' => $e]);
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function create(Request $request)
    {
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
            $loggedUser = $request->user();

            $ownerService = new OwnerFiltersService();
            $result = $ownerService->checkBuildingAccess($request->buildingId);

            if(!$result['access']){
                DB::rollBack();
                return redirect()->back()->withInput($request->except('unitId'))->with('error', $result['message']);
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

            $assignUnitService = new AssignUnitService();
            [$assignedUnit, $transaction] = $assignUnitService->unitAssignment_Transaction($user, $unit, $request->type,null, $request->price, (int) $request->no_of_months);

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

            $userId = $request->userId ?? $user->id;
            $assignUnitService->sendUnitAssignmentNotifications(
                $unit,
                $transaction,
                $userId,
                $assignedUnit,
                $loggedUser
            );

            DB::commit();

            return redirect()->back()->with('success', 'Unit assigned successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in unit assignment : ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }


    // Helper Functions
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

}
