<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\UserBuildingUnit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\BuildingUnit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function toggleMembershipStatus(Request $request)
    {
        return $this->toggleSubscriptionStatus($request, 'Membership');
    }

    public function toggleRentalContractStatus(Request $request)
    {
        return $this->toggleSubscriptionStatus($request, 'Rental Contract');
    }

    private function toggleSubscriptionStatus(Request $request, string $purpose)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        try {
            $user = $request->user();

            $subscription = Subscription::where('user_id', $user->id)->findOrFail($request->subscription_id);
            $originalStatus = $subscription->subscription_status;

            if ($purpose === 'Rental Contract') {
                $rentalContract = UserBuildingUnit::where('user_id', $user->id)
                    ->where('contract_status', 1)
                    ->where('subscription_id', $subscription->id)
                    ->first();

                if (!$rentalContract) {
                    return response()->json([
                        'error' => 'No active rental contract found for this subscription.',
                    ], 400);
                }

                if ($rentalContract->renew_canceled && $originalStatus === 'Canceled') {
                    return response()->json([
                        'error' => 'This rental contract has been officially terminated by the building administration and is no longer eligible for reactivation.',
                    ], 403);
                }
            }

            if ($originalStatus === 'Active') {
                $subscription->subscription_status = 'Canceled';
            } elseif ($originalStatus === 'Canceled') {
                $subscription->subscription_status = 'Active';
            } else {
                return response()->json([
                    'error' => "$purpose with status '{$originalStatus}' cannot be toggled.",
                ], 400);
            }

            $subscription->save();

            return response()->json([
                'message' => "$purpose subscription status changed to '{$subscription->subscription_status}' successfully.",
                'subscription_status' => $subscription->subscription_status,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Invalid Subscription ID.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error toggling subscription status: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to toggle subscription status. Please try again.',
            ], 500);
        }
    }

}
