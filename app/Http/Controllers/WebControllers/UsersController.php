<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\DropdownType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::with('role', 'address')
                    ->when($search, function($query, $search) {
                        return $query->where('name', 'like', "%{$search}%")
                                     ->orWhere('email', 'like', "%{$search}%")
                                     ->orWhere('phone_no', 'like', "%{$search}%")
                                     ->orWhereHas('address', function($query) use ($search) {
                                         $query->where('city', 'like', "%{$search}%");
                                     })
                                     ->orWhere('status', 'like', "%{$search}%") // Matching with the status
                                     ->orWhereHas('role', function($query) use ($search) {
                                         $query->where('name', 'like', "%{$search}%"); // Matching with the role name
                                     });
                    })
                    ->paginate(10);


        return view('Heights.Admin.Users.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::pluck('name','id')->all();
        $dropdownData = DropdownType::with(['values.childs.childs'])->where('type_name', 'Country')->get(); // Country -> Province -> City

        return view('Heights.Admin.Users.create',compact('roles', 'dropdownData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone_no' => ['nullable', 'string', 'max:20'],
            'cnic' => ['nullable','max:18', 'unique:users,cnic'],
            'picture' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:6048'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'date_of_birth' => 'nullable|date',
            // 'organization_id' => ['nullable', 'integer', 'exists:organizations,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:50'],
            'province' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:50'],
            'postal_code' => ['nullable', 'string', 'max:50'],
        ]);

        $password = "Admin@123";

        // Handle profile image upload
        $profileImageName = null;
        if ($request->hasFile('picture')) {
            $profileImage = $request->file('picture');
            $profileImageName = time() . '_' . $profileImage->getClientOriginalName();
            $profileImagePath = 'uploads/users/images/' . $profileImageName;
            $profileImage->move(public_path('uploads/users/images'), $profileImageName);
        }

        $address = Address::create([
            'location' => $request->location,
            'country' => $request->location,
            'province' => $request->location,
            'city' => $request->location,
            'postal_code' => $request->location,
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'phone_no' => $request->phone_no,
            'cnic' => $request->cnic,
            'picture' => $profileImagePath, // Store the filename with path,
            'gender' => $request->gender,
            'role_id' => $request->role_id,
            'address_id' => $address->id,
            'date_of_birth' => $request->date_of_birth,
            'status' => 1,

        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');

    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('role')->findorfail($id);

        return response()->json([
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('role','address')->findOrFail($id);
        $roles = Role::all();


        $dropdownData = DropdownType::with(['values.childs.childs'])->where('type_name', 'Country')->get(); // Country -> Province -> City

        return view('Heights.Admin.Users.edit',compact('user', 'roles', 'dropdownData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'phone_no' => 'nullable|string|max:15',
            'cnic' => 'nullable|string|max:18|unique:users,cnic,' . $id,
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role_id' => 'nullable|exists:roles,id',
            'date_of_birth' => 'nullable|date',
            // 'organization_id' => 'nullable|exists:organizations,id',
            'location' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            'postal_code' => 'nullable', 'string', 'max:50',
            'status' => 'required|integer|in:0,1',
        ]);

        // Fetch the user by ID
        $user = User::findOrFail($id);
        $address = Address::findOrFail($user->address_id);

        $address->update([
            'location' => $request->location,
            'country' => $request->country,
            'province' => $request->province,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
        ]);

        if ($request->hasFile('picture')) {
            $profileImage = $request->file('picture');
            $profileImageName = time() . '_' . $profileImage->getClientOriginalName();
            $profileImagePath = 'uploads/users/images/' . $profileImageName;

            $validatedData['picture'] = $user->picture;
            if ($user->picture != $profileImageName) {

                $profileImage->move(public_path('uploads/users/images/'), $profileImageName);

                $oldProfileImagePath = public_path($user->picture);
                if (File::exists($oldProfileImagePath)) {
                    File::delete($oldProfileImagePath);
                }

                $validatedData['picture'] = $profileImagePath;
            }
        }

        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => $request->filled('password') ? Hash::make($validatedData['password']) : $user->password,
            'phone_no' => $validatedData['phone_no'],
            'cnic' => $validatedData['cnic'],
            'gender' => $validatedData['gender'],
            'picture' => $validatedData['picture'] ?? $user->picture,
            'role_id' => $validatedData['role_id'],
            'status' => $validatedData['status'],
            'date_of_birth' => $validatedData['date_of_birth'],
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Fetch the user by ID
        $user = User::findOrFail($id);

        // Delete the user's picture if it exists
        if ($user->picture) {
            $oldProfileImagePath = public_path('uploads/users/images/' . $user->picture);
            if (File::exists($oldProfileImagePath)) {
                File::delete($oldProfileImagePath);
            }
        }

        // Delete the user
        $user->delete();

        // Redirect to the users index page with a success message
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }


}
