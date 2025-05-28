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
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->cookie('jwt_token') ?? $this->getTokenFromHeader($request);

            if (!$token) {
                return $this->handleResponse($request,'Authorization required', 401);
            }

            $user = JWTAuth::setToken($token)->authenticate();
            $payload = JWTAuth::setToken($token)->getPayload();
            $tokenData = $payload['user'] ?? null;

            $currentIp = $request->ip();
            $currentAgent = $request->header('User-Agent');

            $tokenIp = $tokenData['ip'] ?? null;
            $tokenAgent = $tokenData['agent'] ?? null;

            if (!$user || $user->status === 0) {
                return $this->handleResponse($request,'User account is deactivated or deleted by administrator', 401);
            }

            if (!$tokenData) {
                return $this->handleResponse($request,'Invalid session ID', 401);
            }

            if ($tokenIp !== $currentIp || $tokenAgent !== $currentAgent) {
                return $this->handleResponse($request, 'For security reasons, we have terminated your session. Please log in again.', 401);
            }

            if ($tokenData['role_id'] !== $user->role_id) {
                return $this->handleResponse($request,'Your role has been changed by administrator', 401);
            }

            $request->attributes->set('token', $tokenData);
            $request->merge(['user' => $user]);

            return $next($request);

        } catch (TokenExpiredException $e) {
            return $this->handleResponse($request,'Session has expired', 401);
        } catch (TokenInvalidException $e) {
            return $this->handleResponse($request,'Invalid session ID', 401);
        } catch (JWTException $e) {
            return $this->handleResponse($request,'Could not parse token', 401);
        } catch (Exception $e) {
            return $this->handleResponse($request,'Invalid or expired session ID', 401);
        }
    }

    private function getTokenFromHeader(Request $request)
    {
        $authorizationHeader = $request->header('Authorization');
        if ($authorizationHeader) {
            $parts = explode(' ', $authorizationHeader);
            if (count($parts) === 2 && $parts[0] === 'Bearer') {
                return $parts[1];
            }
        }
        return null;
    }

    private function handleResponse(Request $request, string $message, int $statusCode)
    {
        if ($request->wantsJson()) {
            return response()->json(['error' => $message], $statusCode);
        }

        $forgetToken = cookie()->forget('jwt_token');
        return redirect()->route('login')->with('error', $message)->withCookie($forgetToken);
    }

}
