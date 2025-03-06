<?php

namespace App\Http\Controllers\GeneralControllers;

use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTFactory;

class ForgotPasswordController extends Controller
{
    public function showForgetForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $payload = JWTFactory::customClaims([
            'sub' => 'reset-password',
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addMinutes(5)->timestamp,
            'nbf' => Carbon::now()->timestamp,
            'message' => 'Reset Token'
        ])->make();
        $token = JWTAuth::encode($payload)->get();

        $storeToken = User::where('email', $request->email)->update([
            'reset_token' => $token,
        ]);

        if (!$storeToken) {
            return $this->handleResponse($request, 404,'error','Something went wrong');
        }

        try {
            Mail::send('auth.password-reset', ['token' => $token], function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Password Reset Link');
            });
        } catch (\Exception $e) {
            return $this->handleResponse($request, 500, 'error', 'Failed to send email: ' . $e->getMessage());
        }
        return $this->handleResponse($request, 200,'success', 'A password reset link has been sent to your email.');
    }

    public function showResetForm($token)
    {
        if (!$token) {
            return view('auth.reset-password-error', ['error' => 'Token is required.']);
        }

        try {
            $decoded = JWTAuth::setToken($token)->getPayload();
            $user = User::where('reset_token', $token)->first();

            if (!$user) {
                return view('components.unauthorized-access', [
                    'error_code' => '410',
                    'message' => 'This link is already used.']);
            }

            return view('auth.reset-password', ['token' => $token]);

        } catch (TokenExpiredException $e) {
            return view('components.unauthorized-access', [
                'error_code' => '403',
                'message' => 'Reset Link has expired.']);
        } catch (JWTException $e) {
            return view('components.unauthorized-access', [
                'error_code' => '403',
                'message' => 'Invalid Reset Link.']);
        } catch (\Exception $e) {
            Log::error("Error in validateResetPassword: " . $e->getMessage());
            return view('components.unauthorized-access', [
                'error_code' => '500',
                'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'newPassword' => 'required|min:8|max:20',
                'confirmPassword' => 'required|same:newPassword',
            ]);

            $token = $request->token;
            $newPassword = $request->newPassword;

            try {
                JWTAuth::setToken($token)->getPayload();
            } catch (TokenExpiredException $e) {
                return redirect()->back()->with('error', 'Reset Link has expired.');
            } catch (JWTException $e) {
                return redirect()->back()->with('error', 'Invalid Reset Link.');
            }

            $user = User::where('reset_token', $token)->first();
            if (!$user) {
                return redirect()->back()->with('error', 'Invalid or expired Link.');
            }

            $user->update([
                'password' => Hash::make($newPassword),
                'reset_token' => null,
            ]);

            return redirect()->back()->with('success', 'Password reset successful.');
        } catch (\Exception $e) {
            Log::error("Error in resetPassword: " . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
