<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class FinanceController extends Controller
{
    public function organizationTopSixTransactions(Request $request)
    {
        $token = $request->attributes->get('token');

        if (!$token || empty($token['organization_id'])) {
            return response()->json(['error' => 'This info is for Organization owners'], 401);
        }

        $organization_id = $token['organization_id'];

        try {
            $transactions = Transaction::where(function ($query) use ($organization_id) {
                $query->where(function ($q) use ($organization_id) {
                    $q->where('buyer_type', 'organization')
                        ->where('buyer_id', $organization_id);
                })->orWhere(function ($q) use ($organization_id) {
                    $q->where('seller_type', 'organization')
                        ->where('seller_id', $organization_id);
                });
            })
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();

            $history = $transactions->map(function ($txn) use ($organization_id) {
                $isBuyer = $txn->buyer_type === 'organization' && $txn->buyer_id == $organization_id;

                return [
                    'id' => $txn->id,
                    'title' => $txn->transaction_title,
                    'type' => $isBuyer ? $txn->buyer_transaction_type : $txn->seller_transaction_type,
                    'price' => number_format($txn->price, 2) . ' ' . $txn->currency,
                    'status' => $txn->status,
                    'created_at' => $txn->created_at->diffForHumans(),
                ];
            });

            return response()->json(['history' => $history]);

        } catch (\Exception $e) {
            Log::error('Transaction history fetch failed: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching transaction history.'], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }

            $transaction = Transaction::where(function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('buyer_type', 'user')->where('buyer_id', $user->id);
                })->orWhere(function ($q) use ($user) {
                    $q->where('seller_type', 'user')->where('seller_id', $user->id);
                });
            })->where('id', $id)
                ->first();


            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found.'], 404);
            }

            $isBuyer = $transaction->buyer_id === $user->id;

            $transactionDetail = [
                'transaction_id' => $transaction->id,
                'title' => $transaction->transaction_title,
                'type' => $isBuyer ? $transaction->buyer_transaction_type : $transaction->seller_transaction_type,
                'price' => number_format($transaction->price, 2) . ' ' . $transaction->currency,
                'status' => $transaction->status,
                'created_at' => $transaction->created_at->diffForHumans(),
                'payment_method' => $transaction->payment_method,
                'is_subscription' => $transaction->is_subscription,
                'subscription_details' => [
                    'start_date' => $transaction->subscription_start_date ? $transaction->subscription_start_date->format('Y-m-d H:i:s') : null,
                    'end_date' => $transaction->subscription_end_date ? $transaction->subscription_end_date->format('Y-m-d H:i:s') : null,
                    'billing_cycle' => $transaction->billing_cycle,
                ],
            ];

            return response()->json(['transaction' => $transactionDetail]);

        } catch (\Exception $e) {
            Log::error('Transaction details fetch failed: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching transaction details.'], 500);
        }
    }

}
