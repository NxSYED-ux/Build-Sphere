<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BillingCycle;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class UpgradePlanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return response()->json(['error' => "Can't access this page, unless you are an organization owner."]);
            }

            $organization_id = $token['organization_id'];

            $subscription = Subscription::where('organization_id', $organization_id)
                ->where('source_name', 'plan')
                ->first();

            $activePlanId = $subscription?->source_id;
            $activeCycle = $subscription?->billing_cycle;
            $planCycles = BillingCycle::pluck('duration_months');

            return view('Owner.Plan.upgrade', compact(
                'planCycles',
                'activePlanId',
                'activeCycle'
            ));
        } catch (\Exception $e) {
            Log::error('Error in Upgrade Plan index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

}
