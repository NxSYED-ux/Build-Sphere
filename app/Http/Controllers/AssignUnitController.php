<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\BuildingUnit;

use Illuminate\Http\Request;

class AssignUnitController extends Controller
{
    public function index(Request $request)
    {
        $selectedUnitId = $request->input('unit_id', null);
        $selectedUserId = $request->input('user_id', null);

        //$users = User::where('role_id',4)->pluck('name', 'id');  //Assuming Users role_id = 4 and use this only when you are 100% sure that this is the id and can never delete
        $users = User::select('id', 'name')->whereHas('role', fn ($query) => $query->where('name', 'User'))->get();    // otherwise use this
        $units = BuildingUnit::select('id', 'unit_name')->whereDoesntHave('userUnits', fn($q) => $q->where('contract_status', 1)) ->get();

        return view('your-view-name', compact('selectedUnitId','selectedUserId' , 'users', 'units'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
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
            Log::error('Unit assignment failed: ' . $e->getMessage());
            // return 'error' => 'Internal server error.'
        }

    }
}
