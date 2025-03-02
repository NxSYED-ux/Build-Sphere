<?php

namespace App\Http\Controllers\GeneralControllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // View Profile:
    public function getProfile(Request $request, string $role = 'staff-user')
    {
        $user = $request->user();
        $token =  $request->attributes->get('token');
        $role_name =  $token['role_name'];

        $address = Address::where('id', $user->address_id)
            ->select('location', 'city', 'province', 'country', 'postal_code')
            ->first();

        $userData = (object)[
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_no' => $user->phone_no,
            'cnic' => $user->cnic,
            'gender' => $user->gender,
            'picture' => $user->picture,
            'date_of_birth' => $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : null,
            'address' => $address,
            'role' => (object)[
                'name' => $role_name,
            ],
        ];

        $view = match ($role) {
            'admin' => 'Heights.Admin.Profile.admin_profile',
            'owner' => 'Heights.Owner.Profile.owner_profile',
            'staff-user' => null,
            default => abort(404, 'Page not found'),
        };

        return $this->handleResponse($request, 200, 'user', $userData, $view);
    }
    public function adminProfile(Request $request): View
    {
        return $this->getProfile($request, 'admin');
    }
    public function ownerProfile(Request $request): View
    {
        return $this->getProfile($request, 'owner');
    }

    // Update Profile:
    public function updateProfileData(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'User ID is required'], 400);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:50',
                'phone_no' => 'nullable|string|max:20',
                'cnic' => 'nullable|string|max:25',
                'gender' => 'nullable|string|max:10',
                'date_of_birth' => 'nullable|date',
                'address.location' => 'nullable|string|max:255',
                'address.city' => 'nullable|string|max:50',
                'address.province' => 'nullable|string|max:50',
                'address.country' => 'nullable|string|max:50',
            ]);

            $userUpdated = $user->update(array_filter($validated));

            if (!$userUpdated) {
                DB::rollBack();
                return $this->handleResponse($request,404,'error','User not found or no changes detected.');
            }

            if ($request->has('address') && $user->address_id) {
                $address = Address::find($user->address_id);

                if (!$address) {
                    DB::rollBack();
                    return response()->json(['error' => 'Address not found.'], 404);
                }

                $addressUpdated = $address->update(array_filter($request->input('address')));

                if (!$addressUpdated) {
                    DB::rollBack();
                    return response()->json(['error' => 'No address changes detected.'], 404);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Profile updated successfully.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in updateProfileData: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating user profile data.'], 500);
        }
    }

    public function updatePersonal(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_no' => 'nullable|string|max:15',
            'cnic' => 'nullable|string|max:18|unique:users,cnic,' . $id,
            'date_of_birth' => 'nullable|date',
        ]);

        $user = User::findOrFail($id);

        $user->name = $validatedData['name'];
        $user->phone_no = $validatedData['phone_no'];
        $user->cnic = $validatedData['cnic'];
        $user->date_of_birth = $validatedData['date_of_birth'];
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}
