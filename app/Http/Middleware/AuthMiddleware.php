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
                        return response()->json(['error' => 'Authorization format is invalid. Use Bearer <token>'], 400);
                    }
                }
            }
            elseif ($tokenSource === 'cookie') {
                $token = $request->cookie('jwt_token');
            }

            if (!$token) {
                return $tokenSource === 'header'
                    ? response()->json(['error' => 'Authorization required'], 401)
                    : redirect('/login')->with('error', 'Authorization required');
            }

            $user = JWTAuth::setToken($token)->authenticate();
            $payload = JWTAuth::setToken($token)->getPayload();
            $tokenData = $payload['user'] ?? null;

            if (!$user || $user->status === 0) {
                return $tokenSource === 'header'
                    ? response()->json(['error' => 'User account is deactivated or deleted by administrator'], 403)
                    : redirect('/login')->with('error', 'User account is deactivated or deleted by administrator');
            }

            if ($tokenData['role_id'] !== $user->role_id) {
                return $tokenSource === 'header'
                    ? response()->json(['error' => 'Your role has been changed by administrator'], 403)
                    : redirect('/login')->with('error', 'Your role has been changed by administrator');
            }

            if (!$tokenData) {
                return $tokenSource === 'header'
                    ? response()->json(['error' => 'Malformed session'], 400)
                    : redirect('/login')->with('error', 'Your session is malformed');
            }

            $request->merge(['user' => $user]);
            $request->attributes->set('token', $tokenData);
            return $next($request);

        } catch (TokenExpiredException $e) {
            return $tokenSource === 'header'
                ? response()->json(['error' => 'Session has expired'], 401)
                : redirect('/login')->with('error', 'Session has expired');
        } catch (TokenInvalidException $e) {
            return $tokenSource === 'header'
                ? response()->json(['error' => 'Invalid session ID'], 400)
                : redirect('/login')->with('error', 'Invalid session ID');
        } catch (JWTException $e) {
            return $tokenSource === 'header'
                ? response()->json(['error' => 'Could not parse token'], 400)
                : redirect('/login')->with('error', 'Could not parse token');
        } catch (Exception $e) {
            return $tokenSource === 'header'
                ? response()->json(['error' => 'Invalid or expired session ID'], 400)
                : redirect('/login')->with('error', 'Invalid or expired session ID');
        }
    }
}
