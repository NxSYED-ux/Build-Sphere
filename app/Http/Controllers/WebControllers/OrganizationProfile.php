<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSuccessfulCheckout;
use App\Models\Address;
use App\Models\BillingCycle;
use App\Models\Building;
use App\Models\DropdownType;
use App\Models\Organization;
use App\Models\OrganizationPicture;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class OrganizationProfile extends Controller
{

    public function index()
    {
        try {


            return view('Heights.Owner.Organizations.index');
        } catch (\Exception $e) {
            Log::error("Error in index method: " . $e->getMessage());
            return back()->with('error', 'An error occurred while fetching data.');
        }
    }

    public function edit(string $id)
    {
        $organization = Organization::with('address', 'pictures')->findOrFail($id);
        $dropdownData = DropdownType::with(['values.childs.childs'])->where('type_name', 'Country')->get(); // Country -> Province -> City

        return response()->json([
            'organization' => $organization,
            'dropdowns' => $dropdownData,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:organizations,name,' . $id . ',id',
            'email' => 'required|string|email|max:255|unique:organizations,email,' . $id . ',id',
            'phone' => 'required|string|max:255|unique:organizations,phone,' . $id . ',id',
            'location' => 'required|string|max:255',
            'country' => 'required|string|max:50',
            'province' => 'required|string|max:50',
            'city' => 'required|string|max:50',
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
                'email' => $request->email,
                'phone' => $request->phone,
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

            return response()->json(['message' => 'Organization updated successfully.']);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error("Error in update method: " . $e->getMessage());
            return response()->json(['error' => 'Organization or address not found.'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in update method: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Try again later.'], 500);
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
