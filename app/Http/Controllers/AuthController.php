<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\LoginRequest;


class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');

        try {
            $user = User::where('email', $email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return redirect()->back()->withErrors(['password' => 'Password is incorrect.'])->withInput(); 
            }

            $token = JWTAuth::fromUser($user); 
            $cookie = cookie('jwt_token', $token);

            // 1 -> Admin, 2 -> Owner, 3 -> Manager, 4 -> Staff, 5 -> User
            if ($user->role_id === 1) {
                return redirect()->route('admin_dashboard')->with('success', 'Login successful')->cookie($cookie);
            } elseif ($user->role_id === 2 || $user->role_id === 3) {
                return redirect()->route('owner_manager_dashboard')->with('success', 'Login successful')->cookie($cookie);
            } else {
                return response()->json([
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role_id' => $user->role_id,
                    ],
                ], 200);
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Internal server error. Please try again.');
        }
    }   

    public function destroy(Request $request): RedirectResponse
    {
        try {
            $token = $request->header('Authorization') ?? $request->cookie('jwt_token');
            if ($token) {
                if (strpos($token, 'Bearer ') === 0) {
                    $token = str_replace('Bearer ', '', $token);
                }else{
                    $cookie = cookie('jwt_token', null, -1);
                }
                // JWTAuth::setToken($token);   JWTAuth::invalidate();  // Not working
                return redirect('/login')->with('success', 'Logged out successfully')->cookie($cookie);
            }
            return redirect('/login')->with('error', 'No token found, already logged out.');

        } catch (JWTException $e) {
            return redirect('/login')->with('error', 'Failed to log out, please try again.');
        }
    }
}
