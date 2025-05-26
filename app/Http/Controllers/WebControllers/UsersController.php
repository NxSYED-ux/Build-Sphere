<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\DropdownType;
use App\Models\Role;
use App\Models\User;
use App\Notifications\CredentialsEmail;
use App\Services\AdminFiltersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Customer;
use Stripe\Stripe;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $selectedRole = $request->input('role_id');
            $SelectedStatus = $request->input('status');

            $userQuery = User::with('role', 'address')
                ->where('id', '!=', $request->user()->id);

            if ($search) {
                $userQuery->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_no', 'like', "%{$search}%");
                });
            }

            if ($selectedRole) {
                $userQuery->where('role_id', $selectedRole);
            }

            if (!is_null($SelectedStatus)) {
                $userQuery->where('status', $SelectedStatus);
            }

            $users = $userQuery->paginate(12);

            $adminService = new AdminFiltersService();
            $roles = $adminService->roles();

            return view('Heights.Admin.Users.index', compact('users', 'roles'));

        } catch (\Throwable $e) {
            Log::error('Error in users index: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function create()
    {
        try {
            $adminService = new AdminFiltersService();
            $roles = $adminService->roles();
            $dropdownData = DropdownType::with(['values.childs.childs'])
                ->where('type_name', 'Country')
                ->get();

            return view('Heights.Admin.Users.create', compact('roles', 'dropdownData'));
        } catch (\Throwable $e) {
            Log::error('Error in User Controller Create' . $e->getMessage());
            return back()->with('error', 'Something went wrong while loading the form. Please try again.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['bail', 'required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['bail', 'required', 'integer', 'exists:roles,id'],
            'phone_no' => ['bail', 'required', 'string', 'max:20'],
            'cnic' => ['bail', 'required', 'max:18', 'unique:users,cnic'],
            'picture' => ['bail', 'nullable', 'file', 'mimes:png,jpg,jpeg', 'max:5120'],
            'gender' => ['bail', 'required', 'in:Male,Female,Other'],
            'date_of_birth' => ['bail', 'required', 'date'],
            'country' => ['bail', 'required', 'string', 'max:50'],
            'province' => ['bail', 'required', 'string', 'max:50'],
            'city' => ['bail', 'required', 'string', 'max:50'],
            'location' => ['bail', 'required', 'string', 'max:255'],
            'postal_code' => ['bail', 'required', 'string', 'max:50'],
        ]);

        $password = Str::random(8);
        $profileImagePath = null;
        if($request->hasFile('picture')){
            $profileImagePath = $this->handleFileUpload($request);
        }

        DB::beginTransaction();
        try {

            Stripe::setApiKey(config('services.stripe.secret'));

            $stripeCustomer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone_no,
                'address' => [
                    'line1' => $request->location,
                    'city' => $request->city,
                    'state' => $request->province,
                    'postal_code' => $request->postal_code,
                    'country' => $request->country,
                ],
            ]);

            $address = Address::create([
                'location' => $request->location,
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'phone_no' => $request->phone_no,
                'cnic' => $request->cnic,
                'picture' => $profileImagePath,
                'gender' => $request->gender,
                'role_id' => $request->role_id,
                'address_id' => $address->id,
                'date_of_birth' => $request->date_of_birth,
                'customer_payment_id' => $stripeCustomer->id,
            ]);

            DB::commit();

            $user->notify( new CredentialsEmail(
                $user->name,
                $user->email,
                $password,
            ));

            return redirect()->route('users.index')->with('success', 'User created successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('User creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create user. Please try again.');
        }
    }

    public function show(string $id)
    {
        try {
            $user = User::with('role', 'address')->findOrFail($id);

            return response()->json([
                'user' => $user,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('User show failed: ' . $e->getMessage());
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function edit(string $id)
    {
        try {
            $adminService = new AdminFiltersService();
            $roles = $adminService->roles();
            $user = User::with('role', 'address')->findOrFail($id);
            $dropdownData = DropdownType::with(['values.childs.childs'])
                ->where('type_name', 'Country')
                ->get();

            return view('Heights.Admin.Users.edit', compact('user', 'roles', 'dropdownData'));
        } catch (\Throwable $e) {
            Log::error('User edit failed: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Something went wrong while loading the edit form. Please try again.');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user_id . ',id',
            'phone_no' => 'required|string|max:15',
            'cnic' => 'required|string|max:18|unique:users,cnic,' . $request->user_id . ',id',
            'gender' => ['required', 'in:Male,Female,Other'],
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role_id' => 'required|exists:roles,id',
            'date_of_birth' => 'required|date',
            'location' => 'required|string|max:255',
            'country' => 'required|string|max:50',
            'province' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'postal_code' => 'required|string|max:50',
            'status' => 'required|integer|in:0,1',
            'updated_at' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $user = User::where([
                ['id', '=', $request->user_id],
                ['updated_at', '=', $request->updated_at]
            ])->sharedLock()->first();

            if (!$user) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Please refresh the page and try again.');
            }

            $address = Address::findOrFail($user->address_id);

            if ($request->hasFile('picture')) {
                $profileImagePath = $this->handleFileUpload($request);
                $oldProfileImagePath = public_path($user->picture);
                if (File::exists($oldProfileImagePath)) {
                    File::delete($oldProfileImagePath);
                }
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'cnic' => $request->cnic,
                'gender' => $request->gender,
                'picture' => $profileImagePath ?? $user->picture,
                'role_id' => $request->role_id,
                'status' => $request->status,
                'date_of_birth' => $request->date_of_birth,
                'updated_at' => now()
            ]);

            $address->update([
                'location' => $request->location,
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

            DB::commit();
            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update user. Please try again.');
        }
    }


    // Helper Functions
    private function handleFileUpload(Request $request): ?string
    {
        $profileImage = $request->file('picture');
        $profileImageName = time() . '_' . $profileImage->getClientOriginalName();
        $profileImagePath = 'uploads/users/images/' . $profileImageName;
        $profileImage->move(public_path('uploads/users/images'), $profileImageName);
        return $profileImagePath;
    }

}
