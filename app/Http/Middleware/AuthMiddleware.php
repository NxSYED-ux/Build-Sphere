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

            $userData = JWTAuth::setToken($token)->authenticate();
            $payload = JWTAuth::setToken($token)->getPayload();

            Log::info('User Payload:', [$payload]);
            $user = $payload['user'];
            Log::info('User Data:', [$user]);

            if (!$user) {
                return response()->json(['message' => 'Invalid or expired token'], 403);
            }

            $request->merge(['user' => $user]);

        } catch (TokenExpiredException $e) {
            return redirect('/login')->with('error', 'Token has expired');
        } catch (TokenInvalidException $e) {
            return redirect('/login')->with('error', 'Invalid token');
        } catch (JWTException $e) {
            return redirect('/login')->with('error', 'Could not parse token');
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Invalid or expired token');
        }

        return $next($request);
    }
}
