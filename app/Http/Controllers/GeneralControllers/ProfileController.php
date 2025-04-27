<?php

namespace App\Http\Controllers\GeneralControllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Address;
use App\Models\DropdownType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
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

        $dropdownData = null;
        if(!$request->wantsJson()){
            $dropdownData = DropdownType::with(['values.childs.childs'])
                ->where('type_name', 'Country')
                ->get();
        }

        $userData = (object)[
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_no' => $user->phone_no,
            'cnic' => $user->cnic,
            'gender' => $user->gender,
            'status' => $user->status,
            'picture' => $user->picture,
            'date_of_birth' => $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : null,
            'address' => $address,
            'updated_at' => $user->updated_at,
            'role' => (object)[
                'name' => $role_name,
            ],
            'dropdownData' => $dropdownData,
        ];

        $view = match ($role) {
            'admin' => 'Heights.Admin.Profile.admin_profile',
            'owner' => 'Heights.Owner.Profile.owner_profile',
            'staff-user' => null,
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
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'User ID is required'], 400);
        }
        $request->validate([
            'name' => 'required|string|max:50',
            'phone_no' => 'nullable|string|max:20',
            'cnic' => 'nullable|string|max:25|unique:users,cnic,' . $user->id . 'id',
            'gender' => 'nullable|string|max:10',
            'date_of_birth' => 'nullable|date',
            'updated_at' => 'required',
            'address' => 'nullable|array',
            'address.location' => 'nullable|string|max:255',
            'address.city' => 'nullable|string|max:50',
            'address.province' => 'nullable|string|max:50',
            'address.country' => 'nullable|string|max:50',
            'address.postal_code' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {

            $checking = User::where([
                ['id', '=', $user->id],
                ['updated_at', '=', $request->updated_at]
            ])->sharedLock()->first();

            if (!$checking) {
                DB::rollBack();
                return $this->handleResponse($request,404,'error','Please refresh the page and try again.');
            }

            $userUpdated = $checking->update([
                'name' => $request->name ?? $user->name,
                'phone_no' => $request->phone_no ?? $user->phone_no,
                'cnic' => $request->cnic ?? $user->cnic,
                'date_of_birth' => $request->date_of_birth ?? $user->date_of_birth,
                'gender' => $request->gender ?? $user->gender,
                'updated_at' => now(),
            ]);

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
                    return $this->handleResponse($request,404,'error','No address changes detected.');
                }
            }

            DB::commit();
            return $this->handleResponse($request,200,'success','Profile updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in updateProfileData: " . $e->getMessage());
            return $this->handleResponse($request,500,'error','Internal Server Error.');
        }
    }


    // Upload Profile Pic
    public function uploadProfilePic(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'User ID is required.'], 400);
            }

            $request->validate([
                'picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $image = $request->file('picture');

            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = 'uploads/users/images/' . $imageName;
            $image->move(public_path('uploads/users/images'), $imageName);

            $oldProfileImagePath = public_path($user->picture);
            if (File::exists($oldProfileImagePath)) {
                File::delete($oldProfileImagePath);
            }

            $user->update(['picture' => $imagePath]);

            return response()->json(['success' => 'Profile picture updated successfully.', 'picture' => $imagePath]);
        } catch (\Exception $e) {
            Log::error("Error in uploadProfilePic: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }


    // Delete Profile Picture
    public function deleteProfilePic(Request $request){
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'User ID is required.'], 400);
            }

            if (!$user->picture) {
                return response()->json(['error' => 'No profile picture to delete.'], 400);
            }

            $oldProfileImagePath = public_path($user->picture);
            if (File::exists($oldProfileImagePath)) {
                File::delete($oldProfileImagePath);
            }

            $user->update(['picture' => null]);

            return response()->json(['success' => 'Profile picture removed successfully.'], 200);

        } catch (\Exception $e) {
            Log::error("Error in Remove Profile Pic: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }


    // Change Password
    public function changePassword(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 400);
            }

            $validated = $request->validate([
                'old_password' => 'required|string|min:8',
                'new_password' => 'required|string|min:8',
                'confirm_password' => 'required|string|min:8|same:new_password',
            ]);

            if (!Hash::check($validated['old_password'], $user->password)) {
                return $this->handleResponse($request, 400, 'error', 'Old password is incorrect.');
            }

            $user->password = Hash::make($validated['new_password']);
            $userUpdated = $user->save();

            if (!$userUpdated) {
                return $this->handleResponse($request, 500, 'error', 'Failed to update password.');
            }

            return $this->handleResponse($request, 200, 'success', 'Password changed successfully.');

        } catch (\Exception $e) {
            Log::error("Error in changePassword: " . $e->getMessage());
            return $this->handleResponse($request, 500, 'error', $e->getMessage());
        }
    }
}
