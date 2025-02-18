<?php

namespace App\Http\Middleware;

use Closure;
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

            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return response()->json(['message' => 'Invalid or expired token'], 403);
            }

            $request->merge(['user' => $user]);

        } catch (TokenExpiredException $e) {
            
            return redirect()->route('login')->with('message', 'Token has expired. Please login again');
            // return response()->json(['message' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['message' => 'Invalid token'], 403);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Could not parse token'], 400);
        } catch (Exception $e) {
            return response()->json(['message' => 'Invalid or expired token'], 403);
        }

        return $next($request);
    }
}
