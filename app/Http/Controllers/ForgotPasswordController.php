<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // Show Forgot Password Form
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    } 

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token = Str::random(60);
 
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // Send email
        Mail::send('auth.password-reset', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Password Reset Request');
        });

        return back()->with('success', 'A password reset link has been sent to your email.');
    }


    // Show Reset Password Form
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    // Handle Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
            'token' => 'required'
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Invalid or expired token.']);
        }
 
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete token after reset
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('message', 'Your password has been reset!');
    }
}
