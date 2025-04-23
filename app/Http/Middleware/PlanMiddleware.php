<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PlanMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->attributes->get('token');
            $user = $request->user();

            if ($user->is_super_admin === 1) {
                return $next($request);
            }

            if (!$token || empty($token['organization_id'])) {
                return $this->handleResponse(
                    $request,
                    'error',
                    'You cannot perform this action because it is not linked to any organization. Please switch to an organization account to proceed.'
                );
            }

            $organization = Organization::select('status')->find($token['organization_id']);

            if (!$organization) {
                return $this->handleResponse(
                    $request,
                    'error',
                    'The organization does not exist.'
                );
            }

            $status = $organization->status;

            if ($status === 'Enable') {
                return $next($request);
            }

            if ($status === 'Disable' && $user->role_id === 2) {
                return $this->handleResponse(
                    $request,
                    'plan_error',
                    'Pay the membership fee, otherwise your organization accounts will be blocked.'
                );
            }

            if ($status === 'Blocked') {
                $heading = $user->role_id === 2 ? 'plan_error' : 'error';
                $message = $user->role_id === 2
                    ? 'Account has been Blocked due to not paying the subscription on time.'
                    : 'Account has been Blocked because the owner did not pay the fees.';

                return $this->handleResponse($request, $heading, $message);
            }

            return $next($request);

        } catch (\Exception $e) {
            Log::error('PlanMiddleware Exception: ' . $e->getMessage());

            return $this->handleResponse(
                $request,
                'error',
                'Something went wrong while verifying your organization plan. Please try again later.'
            );
        }
    }


    private function handleResponse(Request $request, string $heading, string $message, int $statusCode = 403, bool $logout = false)
    {
        if($request->wantsJson()){
            return response()->json(['error' => $message], $statusCode);
        }
        elseif ($logout){
            return redirect()->route('login')->with($heading, $message);
        }else{
            return redirect()->route('owner_manager_dashboard')->with($heading, $message);
        }
    }
}
