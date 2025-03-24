<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Building;
use App\Models\DropdownType;
use App\Models\Organization;
use App\Models\OrganizationPicture;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class OrganizationController extends Controller
{

    public function index()
    {
        try {
            $activeTab = 'Tab1';
            $organizations = Organization::with('address', 'pictures', 'owner')->get();
            $dropdownData = DropdownType::with(['values.childs.childs'])->where('type_name', 'Country')->get(); // Country -> Province -> City
            $owners = User::where('role_id', 2)
                ->whereNotIn('id', Organization::pluck('owner_id'))
                ->pluck('name', 'id');

            return view('Heights.Admin.Organizations.index', compact('organizations', 'activeTab', 'dropdownData', 'owners'));
        } catch (\Exception $e) {
            Log::error("Error in index method: " . $e->getMessage());
            return back()->with('error', 'An error occurred while fetching data.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:organizations,name'],
            'owner_id' => ['required', 'integer','unique:organizations,owner_id'],
            'location' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:50'],
            'province' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:50'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'membership_start_date' => ['required', 'date'],
            'membership_end_date' => ['required', 'date'],
            'organization_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        try {

            $address = Address::create([
                'location' => $request->location,
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

            $organization = Organization::create([
                'name' => $request->name,
                'owner_id' => $request->owner_id,
                'address_id' => $address->id,
                'membership_start_date' => $request->membership_start_date,
                'membership_end_date' => $request->membership_end_date,
            ]);

            if ($request->hasFile('organization_pictures')) {
                foreach ($request->file('organization_pictures') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = 'uploads/organizations/images/' . $imageName;
                    $image->move(public_path('uploads/organizations/images'), $imageName);

                    OrganizationPicture::create([
                        'organization_id' => $organization->id,
                        'file_path' => $imagePath,
                        'file_name' => $imageName,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('organizations.index')->with('success', 'Organization created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the organization.');
        }
    }


    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $organization = Organization::with('address','pictures')->findOrFail($id);
        $dropdownData = DropdownType::with(['values.childs.childs'])->where('type_name', 'Country')->get(); // Country -> Province -> City
        $owners = User::where('role_id',2)->pluck('name', 'id');
        return view('Heights.Admin.Organizations.edit',compact('organization','dropdownData', 'owners'));
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:organizations,name,' . $id,
            'owner_id' => 'required|integer',
            'status' => 'required|string',
            'location' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:50'],
            'province' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:50'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'membership_start_date' => ['required', 'date'],
            'membership_end_date' => ['required', 'date'],
            'organization_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        try {

            $organization = Organization::findOrFail($id);
            $address = Address::findOrFail($organization->address_id);

            $address->update([
                'location' => $request->location,
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

            $organization->update([
                'name' => $request->name,
                'owner_id' => $request->owner_id,
                'address_id' => $address->id,
                'status' => $request->status,
                'membership_start_date' => $request->membership_start_date,
                'membership_end_date' => $request->membership_end_date,
            ]);

            if ($request->hasFile('organization_pictures')) {
                foreach ($request->file('organization_pictures') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = 'uploads/organizations/images/' . $imageName;
                    $image->move(public_path('uploads/organizations/images'), $imageName);
                    OrganizationPicture::create([
                        'organization_id' => $organization->id,
                        'file_path' => $imagePath,
                        'file_name' => $imageName,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('organizations.index')->with('success', 'Organization updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the organization.');
        }
    }

    public function getBuildingsAdmin($id)
    {
        try {
            $buildings = Building::where('organization_id', $id)
                ->whereNotIn('status', ['Under Processing', 'Under Review', 'Rejected'])
                ->pluck('name', 'id');

            return response()->json(['buildings' => $buildings]);

        } catch (\Exception $e) {
            Log::error('Error fetching buildings: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }


    public function destroyImage(string $id)
    {
        $image = OrganizationPicture::findOrFail($id);

        if ($image) {
            $oldImagePath = public_path($image->file_path); // Corrected variable name
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            // Delete the image record from the database
            $image->delete();
        }

        return response()->json(['success' => true]);
    }
}
