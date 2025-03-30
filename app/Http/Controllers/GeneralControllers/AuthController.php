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


class AuthController extends Controller
{
    // Login Page
    public function index(): View
    {
        return view('auth.login');
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
                            return $this->handleResponse($request, 500, 'error', 'Your organization is blocked or disabled.');
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
            elseif (($platform === 'user-app' && array_key_exists('User Application', $permissions)) || ($platform === 'staff-app' && array_key_exists('Staff Application', $permissions))) {
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

            $token = JWTAuth::fromUser($user);
            return $this->handleResponse($request, 200, 'success','Login successful', $route, $token, $permissions, $user->name, $user->picture);

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
            return $this->handleResponse($request, 500, 'error', '', '/');
        }
    }


    // Response Handler Function
    protected function handleResponse(Request $request, $statusCode, $heading, $data, $redirectTo = null, $token = null, $permissions = null, $name = null, $picture = null)
    {
        if ($request->wantsJson()) {
            return response()->json([
                $heading === 'password' ? 'error' : $heading => $data,
                'token' => $token,
                'permissions' => $permissions,
                'name' => $name,
                'picture' => $picture,
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
    private function listOfPermissions($user){

        $permissions = DB::select("
                SELECT perm.name, perm.header
                FROM permissions perm
                LEFT JOIN userpermissions userPerm
                    ON perm.id = userPerm.permission_id AND userPerm.user_id = ?
                LEFT JOIN rolepermissions rolePerm
                    ON perm.id = rolePerm.permission_id AND rolePerm.role_id = ?
                WHERE COALESCE(userPerm.status, rolePerm.status) = 1
            ", [$user->id, $user->role_id]);

        $permissionNames = collect($permissions)->groupBy('header')->map(function ($group) {
            return $group->pluck('name')->toArray();
        })->toArray();

        return $permissionNames;
    }


    // Organization Status Check
    private function OrganizationAccess($user)
    {
        $organization = null;

        if ($user->role_id === 2) {
            $organization = $user->organization;
        } elseif ($user->staffMember) {
            $organization = Organization::find($user->staffMember->organization_id);
        }

        if (!$organization) {
            return null;
        }

        if ($organization->status === 'Blocked') {
            return false;
        }

        if ($organization->status === 'Disable' && $user->role_id === 2) {
            return false;
        }

        return true;
    }

}
