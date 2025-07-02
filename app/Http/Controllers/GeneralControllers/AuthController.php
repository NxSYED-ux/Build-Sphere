<?php

namespace App\Http\Controllers\GeneralControllers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserFcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Pusher\Pusher;
use Tymon\JWTAuth\JWT;


class AuthController extends Controller
{
    // Login Page
    public function index(Request $request)
    {
        $token = $request->cookie('jwt_token');

        if (!$token) {
            return view('auth.login');
        }

        try {
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return view('auth.login');
            }

            $permissions = $this->listOfPermissions($user);
            $rolesWithOwnerAccess = [2, 3, 4];
            $route = null;

            if (in_array($user->role_id, $rolesWithOwnerAccess, true)) {
                if (array_key_exists('Owner Portal', $permissions)) {
                    $access = $this->OrganizationAccess($user);
                    if ($access === false || $access === null) {
                        unset($permissions['Owner Portal']);
                        return view('auth.login');
                    }
                    $route = 'owner_manager_dashboard';
                } elseif (array_key_exists('Admin Portal', $permissions)) {
                    $route = 'admin_dashboard';
                }
            } else {
                if (array_key_exists('Admin Portal', $permissions)) {
                    $route = 'admin_dashboard';
                } elseif (array_key_exists('Owner Portal', $permissions)) {
                    $route = 'owner_manager_dashboard';
                }
            }

            if ($route) {
                return redirect()->route($route)->with('permissions', $permissions);
            }

            return view('auth.login');

        } catch (\Exception $e) {
            Log::error('Error in login index: ' . $e->getMessage());
            return view('auth.login');
        }
    }


    // Login Functions
    public function login(Request $request, string $platform = 'web')
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

            if($user->is_verified === 0){
                $user->update(['is_verified' => 1]);
            }

            $permissions = $this->listOfPermissions($user);
            $route = null;

            if ($platform === 'web') {
                $rolesWithOwnerAccess = [2, 3, 4];
                if (in_array($user->role_id, $rolesWithOwnerAccess, true)) {
                    if (array_key_exists('Owner Portal', $permissions)) {
                        $route = 'owner_manager_dashboard';
                        $access = $this->OrganizationAccess($user);
                        if ($access === false || $access === null) {
                            unset($permissions['Owner Portal']);
                            return $this->handleResponse(
                                $request, 500, 'error', 'Account is blocked due to unpaid subscription. Please contact the owner.'
                            );
                        }
                    }
                    elseif (array_key_exists('Admin Portal', $permissions)) {
                        $route = 'admin_dashboard';
                    }
                } else {
                    $route = array_key_exists('Admin Portal', $permissions) ? 'admin_dashboard' :
                        (array_key_exists('Owner Portal', $permissions) ? 'owner_manager_dashboard' : null);
                }
            }
            elseif (($platform === 'user-app' && array_key_exists('User Application', $permissions))) {
                $route = 'Access Granted';
            }elseif ($platform === 'staff-app' && array_key_exists('Staff Application', $permissions)){
                $access = $this->OrganizationAccess($user);
                if ($access === false || $access === null) {
                    return $this->handleResponse(
                        $request, 500, 'error', 'Account is blocked due to unpaid subscription. Please contact the owner.'
                    );
                }
                $route = 'Access Granted';
            }

            if(!$route){
                return $this->handleResponse($request, 403, 'error','Access Denied: You don’t have the required permissions.');
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

            // For Web 6 Hours while for mobile 30 days
            $ttlInMinutes = $platform === 'web' ? (6 * 60) : (30 * 24 * 60);

            JWTAuth::factory()->setTTL($ttlInMinutes);
            $token = JWTAuth::fromUser($user);

            $user->update([
                'last_login' => now()
            ]);

            return $this->handleResponse($request, 200, 'login_success','Login successful', $route, $token, $permissions, $user->name, $user->picture, $user->email);

        } catch (\Exception $e) {
            Log::error("Login error: " . $e->getMessage());
            return $this->handleResponse($request, 500, 'error', $e->getMessage());
        }
    }

    public function userLogin(Request $request){
        return $this->login($request, 'user-app');
    }

    public function staffLogin(Request $request){
        return $this->login($request, 'staff-app');
    }


    // Log Out Function
    public function logOut(Request $request)
    {
        try {
            $token = $request->header('Authorization') ?? $request->cookie('jwt_token');

            if ($request->fcmToken) {
                UserFcmToken::where('token', $request->fcmToken)->delete();
            }

            if ($token) {
                JWTAuth::setToken($token)->invalidate(true);
            }

            return $this->handleResponse($request, 200, 'success', 'Logout successful', 'login');
        } catch (JWTException $e) {
            Log::error("Logout error: " . $e->getMessage());
            return $this->handleResponse($request, 500, 'error', 'Something went wrong.', 'login');
        }
    }


    // Response Handler Function
    protected function handleResponse(Request $request, $statusCode, $heading, $data, $redirectTo = null, $token = null, $permissions = null, $name = null, $picture = null, $email = null)
    {
        if ($request->wantsJson()) {
            return response()->json([
                $heading === 'password' ? 'error' : $heading => $data,
                'token' => $token,
                'name' => $name,
                'picture' => $picture,
                'email' => $email,
            ], $statusCode);
        }

        if ($redirectTo) {
            $cookie = $token ? cookie('jwt_token', $token) : cookie()->forget('jwt_token');
            return redirect()->route($redirectTo)->with($heading, $data)->with('permissions', $permissions)->cookie($cookie);
        }
        return redirect()->back()->withErrors([$heading => $data])->withInput();
    }


    // Pusher Authentication Function
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


    // List of Permissions
    private function listOfPermissions($user) {
        try {
            $permissions = DB::select("
            SELECT perm.name, perm.header
            FROM permissions perm
            LEFT JOIN userpermissions userPerm
                ON perm.id = userPerm.permission_id AND userPerm.user_id = ?
            LEFT JOIN rolepermissions rolePerm
                ON perm.id = rolePerm.permission_id AND rolePerm.role_id = ?
            WHERE
                (userPerm.permission_id IS NOT NULL AND rolePerm.permission_id IS NOT NULL AND userPerm.status = 1)
                OR
                (userPerm.permission_id IS NULL AND rolePerm.status = 1);
        ", [$user->id, $user->role_id]);

            return collect($permissions)->groupBy('header')->map(function ($group) {
                return $group->pluck('name')->toArray();
            })->toArray();

        } catch (\Exception $e) {
            Log::error("Error fetching permissions: " . $e->getMessage());
            return [];
        }
    }


    // Organization Status Check
    private function OrganizationAccess($user)
    {
        if ($user->role_id === 2) {
            return true;
        }

        $organization = $user->staffMember ? Organization::find($user->staffMember->organization_id) : null;

        if (!$organization) {
            return null;
        }

        if ($organization->status === 'Blocked') {
            return false;
        }

        return true;
    }

}
