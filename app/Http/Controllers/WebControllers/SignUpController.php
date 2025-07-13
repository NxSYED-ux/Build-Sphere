<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\DropdownType;
use App\Models\Organization;
use App\Models\Otp;
use App\Models\User;
use App\Notifications\OTP_Email;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Stripe\Stripe;
use Stripe\Customer;
use Tymon\JWTAuth\Facades\JWTAuth;


class SignUpController extends Controller
{
    public function index(Request $request)
    {
        $package = $request->query('package');
        $cycle = $request->query('cycle');

        $dropdownData = DropdownType::with(['values.childs.childs'])
            ->where('type_name', 'Country')
            ->get();

        return view('landing-views.ownerSignUp', compact('dropdownData','package', 'cycle'));
    }

    public function send_otp(Request $request)
    {
        try {
            $request->validate([
                'email' => ['bail', 'required', 'string', 'email', 'max:255', 'unique:users,email'],
            ], [
                'email.unique' => 'Email is already registered.',
            ]);

            $email = $request->email;

            $otp = rand(100000, 999999);

            Otp::create([
                'email' => $email,
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(30),
            ]);

            Notification::route('mail', $email)->notify(new OTP_Email($otp));

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in sending OTP' . $e->getMessage());
            return response()->json(['message' => 'Failed to send OTP', 'details' => $e->getMessage()], 500);
        }
    }

    private function verify_otp($email, $otp)
    {
        try {
            $record = Otp::where('email', $email)
                ->where('otp', $otp)
                ->orderByDesc('created_at')
                ->first();

            if (!$record) {
                return ['status' => false, 'message' => 'Invalid OTP.'];
            }

            if ($record->is_used) {
                return ['status' => false, 'message' => 'OTP has already been used.'];
            }

            if (Carbon::now()->greaterThan($record->expires_at)) {
                return ['status' => false, 'message' => 'OTP has expired.'];
            }

            $record->update(['is_used' => 1]);

            return ['status' => true, 'message' => 'OTP verified successfully.'];
        } catch (\Throwable $e) {
            Log::error('Error verifying OTP: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Something went wrong.', 'error' => $e->getMessage()];
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'numeric'],

            'name' => ['bail', 'required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['bail', 'required', 'string', 'min:8'],
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

            'org_name' => ['bail', 'required', 'string', 'max:255', 'unique:organizations,name'],
            'org_email' => ['bail', 'required', 'string', 'email', 'max:255', 'unique:organizations,email'],
            'org_phone' => ['bail', 'required', 'string', 'max:255', 'unique:organizations,phone'],
            'org_picture' => ['bail', 'required', 'file', 'mimes:png,jpg,jpeg', 'max:5120'],
            'org_country' => ['bail', 'required', 'string', 'max:50'],
            'org_province' => ['bail', 'required', 'string', 'max:50'],
            'org_city' => ['bail', 'required', 'string', 'max:50'],
            'org_location' => ['bail', 'required', 'string', 'max:255'],
            'org_postal_code' => ['bail', 'required', 'string', 'max:50'],
        ]);

        DB::beginTransaction();

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $otpCheck = $this->verify_otp($request->email, $request->otp);

            if (!$otpCheck['status']) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', $otpCheck['message']);
            }

            $profileImage = null;
            if($request->hasFile('picture')){
                $profileImage = $this->handleFileUpload($request, 'picture', 'users');
            }

            $organizationImage = null;
            if($request->hasFile('org_picture')){
                $organizationImage = $this->handleFileUpload($request, 'org_picture','organizations', 'logo');
            }

            $customPayload = [
                'org_name' => $request->name,
                'org_email' => $request->email,
                'iat' => now()->timestamp,
            ];

            $token = JWTAuth::customClaims($customPayload)->encode();

            $address = Address::create([
                'location' => $request->location,
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

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

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_no' => $request->phone_no,
                'cnic' => $request->cnic,
                'picture' => $profileImage ? $profileImage['path'] : null,
                'gender' => $request->gender,
                'role_id' => 2,
                'address_id' => $address->id,
                'date_of_birth' => $request->date_of_birth,
                'is_verified' => 1,
                'customer_payment_id' => $stripeCustomer->id,
            ]);

            $org_address = Address::create([
                'location' => $request->org_location,
                'country' => $request->org_country,
                'province' => $request->org_province,
                'city' => $request->org_city,
                'postal_code' => $request->org_postal_code,
            ]);

            $organization = Organization::create([
                'name' => $request->org_name,
                'email' => $request->org_email,
                'phone' => $request->org_phone,
                'logo' => $organizationImage ? $organizationImage['path'] : null,
                'owner_id' => $user->id,
                'address_id' => $org_address->id,
                'status' => 'Disable',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'membership_api_key' => $token,
            ]);

            DB::commit();

            $selectedPackage = $request->input('package');
            $selectedCycle = $request->input('cycle');

            return redirect()->route('checkout', [
                'organization_name' => $organization->name,
                'package' => $selectedPackage,
                'cycle' => $selectedCycle,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to register: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to register. Please try again.');
        }
    }

    private function handleFileUpload(Request $request, string $source, string $folder, string $insideFolder = 'images'): ?array
    {
        if (!$request->hasFile($source) || !$folder) {
            return null;
        }

        $file = $request->file($source);
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = 'uploads/' . $folder . '/' . $insideFolder . '/' . $fileName;

        $file->move(public_path('uploads/' . $folder . '/' . $insideFolder . '/'), $fileName);

        return [
            'path' => $filePath,
            'name' => $fileName,
        ];
    }

}
