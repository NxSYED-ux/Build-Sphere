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
                        return $this->handleResponse($tokenSource, 'Authorization format is invalid. Use Bearer <token>', 400);
                    }
                }
            } elseif ($tokenSource === 'cookie') {
                $token = $request->cookie('jwt_token');
            }

            if (!$token) {
                return $this->handleResponse($tokenSource, 'Authorization required', 401);
            }

            $user = JWTAuth::setToken($token)->authenticate();
            $payload = JWTAuth::setToken($token)->getPayload();
            $tokenData = $payload['user'] ?? null;

            if (!$user || $user->status === 0) {
                return $this->handleResponse($tokenSource, 'User account is deactivated or deleted by administrator', 403);
            }

            if (!$tokenData) {
                return $this->handleResponse($tokenSource, 'Invalid session ID', 400);
            }

            if ($tokenData['role_id'] !== $user->role_id) {
                return $this->handleResponse($tokenSource, 'Your role has been changed by administrator', 403);
            }

            $request->merge(['user' => $user]);
            $request->attributes->set('token', $tokenData);
            return $next($request);

        } catch (TokenExpiredException $e) {
            return $this->handleResponse($tokenSource, 'Session has expired', 401);
        } catch (TokenInvalidException $e) {
            return $this->handleResponse($tokenSource, 'Invalid session ID', 400);
        } catch (JWTException $e) {
            return $this->handleResponse($tokenSource, 'Could not parse token', 400);
        } catch (Exception $e) {
            return $this->handleResponse($tokenSource, 'Invalid or expired session ID', 400);
        }
    }

    private function handleResponse($tokenSource, $message, $statusCode)
    {
        return $tokenSource === 'header'
            ? response()->json(['error' => $message], $statusCode)
            : redirect('/login')->with('error', $message);
    }
}
