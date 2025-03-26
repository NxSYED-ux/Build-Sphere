<?php

namespace App\Http\Controllers\WebControllers;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingUnit;
use App\Models\ManagerBuilding;
use App\Models\User;
use App\Models\UserBuildingUnit;
use App\Models\UserUnitPicture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignUnitController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user() ?? abort(403, 'Unauthorized');
            $token = $request->attributes->get('token');

            $selectedUnitId = $request->input('unit_id');
            $selectedUserId = $request->input('user_id');
            $users = User::where('id', '!=', $user->id)->pluck('name', 'id');
            $units = collect();
            $buildings = collect();

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return view('Heights.Owner.AssignUnits.index', compact('selectedUnitId', 'selectedUserId', 'users', 'units', 'buildings'));
            }

            $organizationId = $token['organization_id'];
            $roleName = $token['role_name'];

            $units = BuildingUnit::select('id', 'unit_name')
                ->where('availability_status', 'Available')
                ->where('organization_id', $organizationId)
                ->get();

            $query = Building::where('organization_id', $organizationId);

            if ($roleName === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
                $query->whereIn('id', $managerBuildingIds);
            }

            $buildings = $query->get();

            return view('Heights.Owner.AssignUnits.index', compact('selectedUnitId', 'selectedUserId', 'users', 'units', 'buildings'));
        } catch (\Throwable $e) {
            Log::error("Error in AssignUnits@index: {$e->getMessage()}", ['exception' => $e]);
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function create()
    {
        //
    }

    public function assignUnit(Request $request)
    {
        $validatedData = $request->validate([
            'userId' => ['required', 'integer', 'exists:users,id'],
            'unitId' => ['required', 'integer', 'exists:buildingunits,id'],
            'type' => ['required', 'in:Rented,Sold'],
            'price' => ['required', 'numeric', 'min:0'],
            'rentStartDate' => ['required_if:type,Rented', 'date', 'nullable'],
            'rentEndDate' => ['required_if:type,Rented', 'date', 'after_or_equal:rentStartDate', 'nullable'],
            'purchaseDate' => ['required_if:type,Sold', 'date', 'nullable'],
            'picture' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        DB::beginTransaction();

        try {
            $existingUnit = UserBuildingUnit::where([
                ['unit_id', '=', $request->unitId],
                // ['user_id', '=', $request->userId], Active this only when one unit can assigned to multiple users at the same time
                ['contract_status', '=', 1]
            ])->exists();

            if ($existingUnit) {
                //return 'error' => 'This unit is already assigned to the user.'
            }

            $assignedUnit = UserBuildingUnit::create([
                'user_id' => $request->userId,
                'unit_id' => $request->unitId,
                'type' => $request->type,
                'price' => $request->price,
                'rent_start_date' => $request->type === 'Rented' ? $request->rentStartDate : null,
                'rent_end_date' => $request->type === 'Rented' ? $request->rentEndDate : null,
                'purchase_date' => $request->type === 'Sold' ? $request->purchaseDate : null,
            ]);

            if ($request->hasFile('picture')) {
                $Image = $request->file('picture');
                $ImageName = time() . '_' . $Image->getClientOriginalName();
                $ImagePath = 'uploads/units/images/' . $ImageName;
                $Image->move(public_path($ImagePath));
                $storeImage = UserUnitPicture::create([
                    'user_unit_id' => $assignedUnit->id,
                    'file_path' => $ImagePath,
                    'file_name' => $ImagePath,
                ]);
            }

            DB::commit();
            //return 'success' => 'Unit assigned successfully.', 'data' => $assignedUnit

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in unit assignment : ' . $e->getMessage());
            // return 'error' => 'Internal server error.'
        }

    }
}
