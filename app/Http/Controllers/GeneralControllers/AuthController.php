<?php

namespace App\Http\Controllers\GeneralControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\UserFcmToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Pusher\Pusher;


class AuthController extends Controller
{
    public function index(): View
    {
        return view('auth.login');
    }

    // Login
    public function adminLogin(LoginRequest $request): RedirectResponse
    {
        return $this->login($request,'admin');
    }
    public function ownerLogin(LoginRequest $request): RedirectResponse
    {
        return $this->login($request,'owner');
    }
    public function login(LoginRequest $request, string $portal = 'staff-user')
    {
        $request->validate([
            'email' => 'required|string|email|max:50',
            'password' => 'required|string|max:20|min:8',
            'newFcmToken' => 'nullable|string'
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->handleResponse($request,400,'password','Invalid email or password.');
            }

            if ($user->status === 0) {
                return $this->handleResponse($request, 500, 'error', 'Your account is inactive. Please contact support.');
            }

            if($request->newFcmToken){
                $existingToken = UserFcmToken::where('token', $request->newFcmToken)->first();

                if ($existingToken) {
                    $existingToken->update(['user_id' => $user->id]);
                } else {
                    UserFcmToken::create([
                        'token' => $request->newFcmToken,
                        'user_id' => $user->id,
                    ]);
                }
            }

            $token = JWTAuth::fromUser($user);

            $route = match ($portal) {
                'admin' => 'admin_dashboard',
                'owner' => 'owner_manager_dashboard',
                'staff-user' => null,
                default => abort(404, 'Page not found'),
            };

            return $this->handleResponse($request, 200, 'success','Login successful', $route, $token);

        } catch (\Exception $e) {
            Log::error("Login error: " . $e->getMessage());
            return $this->handleResponse($request, 500, 'error', $e->getMessage());
        }
    }

    // Log Out
    public function logOut(Request $request)
    {
        try {
            $token = $request->header('Authorization') ?? $request->cookie('jwt_token');

            if ($request->fcmToken) {
                UserFcmToken::where('token', $request->fcmToken)->delete();
            }

            if ($token) {
                JWTAuth::setToken($token)->invalidate(true);
                return $this->handleResponse($request, 200, 'success', 'Logout successful', 'login');
            }
            return $this->handleResponse($request, 200, 'success', 'Logout successful', 'login');
        } catch (JWTException $e) {
            return $this->handleResponse($request, 500, 'error', $e->getMessage(), 'login');
        }
    }


    protected function handleResponse(Request $request, $statusCode, $heading, $data, $redirectTo = null, $token = null)
    {
        if ($request->wantsJson()) {
            return response()->json([
                $heading === 'password' ? 'error' : $heading => $data,
                'token' => $token,
            ], $statusCode);
        }

        if ($redirectTo) {
            $cookie = $token ? cookie('jwt_token', $token) : cookie()->forget('jwt_token');
            return redirect()->route($redirectTo)->with($heading, $data)->cookie($cookie);
        }
        return redirect()->back()->withErrors([$heading => $data])->withInput();
    }

    public function authenticatePusher(Request $request)
    {
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ]
        );

        return response($pusher->authorizeChannel(
            $request->channel_name,
            $request->socket_id
        ));
    }
}
