<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load(['address', 'role']);

        $role = $request->user()->role_id;
        if($role == 1){
            return view('Heights.Admin.Profile.admin_profile', [
                'user' => $user,
            ]);
        }
        else{
            return view('Heights.Owner.Profile.owner_profile', [
                'user' => $user,
            ]);
        }
    }

    public function updatePersonal(Request $request, string $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_no' => 'nullable|string|max:15',
            'cnic' => 'nullable|string|max:18|unique:users,cnic,' . $id,
            'date_of_birth' => 'nullable|date',
        ]);

        // Fetch the user by ID
        $user = User::findOrFail($id);

        // Update the user details
        $user->name = $validatedData['name'];
        $user->phone_no = $validatedData['phone_no'];
        $user->cnic = $validatedData['cnic'];
        $user->date_of_birth = $validatedData['date_of_birth'];
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}
