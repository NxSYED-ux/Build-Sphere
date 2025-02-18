<?php

namespace App\Http\Controllers;
 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\View\View;

class ProfileController extends Controller
{

    public function index(Request $request): View
    {
        $user = $request->user()->load(['address', 'role']);

        $role = $request->user()->role_id;
        if($role == 1){
            return view('Profiles.admin_profile', [
                'user' => $user,
            ]);
        }
        if($role == 2){
            return view('Profiles.owner_profile', [
                'user' => $user,
            ]);
        } 
    }

    public function owner(Request $request): View
    {
        $user = $request->user()->load(['address', 'role']);

        return view('Profiles.owner_profile', [
            'user' => $user,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
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
