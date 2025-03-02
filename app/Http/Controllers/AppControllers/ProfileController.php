<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function userProfile(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $address = Address::where('id', $user->address_id)
                ->select('location', 'city', 'province', 'country')
                ->first();

            return response()->json([
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_no' => $user->phone_no,
                    'cnic' => $user->cnic,
                    'gender' => $user->gender,
                    'picture' => $user->picture,
                    'date_of_birth' => $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : null,
                    'address' => $address
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Error in userProfile: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching user profile data.'], 500);
        }
    }

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
                return response()->json(['error' => 'User not found or no changes detected.'], 404);
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

    public function uploadProfilePic(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'User ID is required'], 400);
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


            return response()->json([
                'message' => 'Profile picture updated successfully!',
                'path' => $imagePath,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage() ?: 'An error occurred while changing the profile picture.'
            ], 500);
        }
    }

}
