<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Http\Request;
use App\Models\User;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next, $tokenSource = 'header')
    {
        try {
            $token = null;

            if ($tokenSource === 'header') {
                $authorizationHeader = $request->header('Authorization');
                if ($authorizationHeader) {
                    $parts = explode(' ', $authorizationHeader);
                    if (count($parts) === 2 && $parts[0] === 'Bearer') {
                        $token = $parts[1];
                    } else {
                        return response()->json(['message' => 'Authorization format is invalid. Use Bearer <token>'], 400);
                    }
                }
            }
            elseif ($tokenSource === 'cookie') {
                $token = $request->cookie('jwt_token');
            }

            if (!$token) {
                return redirect('/login')->with('error', 'Authorization required');
            }

            $payload = JWTAuth::setToken($token)->getPayload();
            $user = $payload['user'];

            $checkUser = User::select('id', 'name')
                ->where([
                    ['id', '=', $user['id']],
                    ['role_id', '=', $user['role_id']],
                    ['status', '=', 1]
                ])->get();

            if ($checkUser->isEmpty()) {
                return redirect('/login')->with('error', 'User account is deactivated or deleted by administrator');
            }

            if (!$user) {
                return redirect('/login')->with('error', 'Your session has been expired');
            }

            $request->merge(['user' => $user]);

        } catch (TokenExpiredException $e) {
            return redirect('/login')->with('error', 'Session has expired');
        } catch (TokenInvalidException $e) {
            return redirect('/login')->with('error', 'Invalid Session id');
        } catch (JWTException $e) {
            return redirect('/login')->with('error', 'Could not parse token');
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Invalid or expired Session Id');
        }

        return $next($request);
    }
}
