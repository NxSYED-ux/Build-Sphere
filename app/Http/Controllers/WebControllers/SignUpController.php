<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Notifications\OTP_Email;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class SignUpController extends Controller
{
    public function index(){
        return view('landing-views.ownerSignUp');
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
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send OTP', 'details' => $e->getMessage()], 500);
        }
    }

}
