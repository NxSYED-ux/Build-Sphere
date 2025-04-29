<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class FinanceController extends Controller
{
    // Index
    public function ownerIndex(Request $request)
    {
        $token = $request->attributes->get('token');

        if (!$token || empty($token['organization_id'])) {
            return redirect()->back()->with('error', 'This info is for Organization owners');
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
                ->paginate(12);

            $history = collect($transactions->items())->map(function ($txn) use ($organization_id) {
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

            return view('Heights.Owner.Finance.index', compact('transactions', 'history'));

        } catch (\Exception $e) {
            Log::error('Transaction history fetch failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching transaction history.');
        }
    }

    public function adminIndex()
    {
        try {
            $transactions = Transaction::where(function ($query) {
                $query->where('seller_type', 'platform')
                    ->orWhere('buyer_type', 'platform');
            })
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            $history = collect($transactions->items())->map(function ($txn) {
                $isPlatformBuyer = $txn->buyer_type === 'platform';

                return [
                    'id' => $txn->id,
                    'title' => $txn->transaction_title,
                    'type' => $isPlatformBuyer ? $txn->buyer_transaction_type : $txn->seller_transaction_type,
                    'price' => number_format($txn->price, 2) . ' ' . $txn->currency,
                    'status' => $txn->status,
                    'created_at' => $txn->created_at->diffForHumans(),
                ];
            });

            return view('Heights.Admin.Finance.index', compact('transactions', 'history'));

        } catch (\Exception $e) {
            Log::error('Transaction history fetch failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching transaction history.');
        }
    }



    // Show
    public function ownerShow($id, Request $request)
    {
        $token = $request->attributes->get('token');

        if (!$token || empty($token['organization_id'])) {
            return redirect()->back()->with('error', 'This info is for Organization owners');
        }

        $organization_id = $token['organization_id'];

        try {
            $transaction = Transaction::with('source', 'source.source')->findOrFail($id);

            $isOrgBuyer = $transaction->buyer_type === 'organization' && $transaction->buyer_id == $organization_id;
            $isOrgSeller = $transaction->seller_type === 'organization' && $transaction->seller_id == $organization_id;

            if (!($isOrgBuyer || $isOrgSeller)) {
                return redirect()->route('owner.finance.index')->with('error', 'Unauthorized access to transaction details.');
            }

            $source = $transaction->source;
            $nestedSource = $source && method_exists($source, 'source') ? $source->source : null;

            $mappedTransaction = [
                'transaction_id' => $transaction->id,
                'title' => $transaction->transaction_title,
                'type' => $isOrgBuyer ? $transaction->buyer_transaction_type : $transaction->seller_transaction_type,
                'price' => number_format($transaction->price, 2) . ' ' . $transaction->currency,
                'status' => $transaction->status,
                'created_at' => $transaction->created_at->diffForHumans(),
                'payment_method' => $transaction->payment_method,
                'is_subscription' => $transaction->is_subscription,
                'subscription_details' => [
                    'start_date' => optional($transaction->subscription_start_date)->format('Y-m-d H:i:s'),
                    'end_date' => optional($transaction->subscription_end_date)->format('Y-m-d H:i:s'),
                    'billing_cycle' => $transaction->billing_cycle,
                ],
                'source' => $source ? [
                    'id' => $source->id,
                    'type' => $transaction->source_name === 'user_building_unit' ? 'Unit Sold' : $transaction->source_name,
                    'details' => $source->toArray(),
                    'nested_source' => $nestedSource ? [
                        'id' => $nestedSource->id ?? null,
                        'type' => $source->source_name ?? null,
                        'details' => $nestedSource->toArray(),
                    ] : null,
                ] : null,
            ];

            return view('Heights.Owner.Finance.show', [
                'transaction' => $mappedTransaction,
            ]);

        } catch (\Throwable $e) {
            Log::error('Transaction detail fetch failed: ' . $e->getMessage());
            return redirect()->route('owner.finance.index')->with('error', 'Transaction not found.');
        }
    }


    public function adminShow($id)
    {
        try {
            $transaction = Transaction::with('source')->findOrFail($id);

            $isPlatformBuyer = $transaction->buyer_type === 'platform';
            $isPlatformSeller = $transaction->seller_type === 'platform';

            if (!($isPlatformBuyer || $isPlatformSeller)) {
                return redirect()->route('admin.finance.index')->with('error', 'Unauthorized access to transaction details.');
            }

            $mappedTransaction = [
                'transaction_id' => $transaction->id,
                'title' => $transaction->transaction_title,
                'type' => $isPlatformBuyer ? $transaction->buyer_transaction_type : $transaction->seller_transaction_type,
                'price' => number_format($transaction->price, 2) . ' ' . $transaction->currency,
                'status' => $transaction->status,
                'created_at' => $transaction->created_at->diffForHumans(),
                'payment_method' => $transaction->payment_method,
                'is_subscription' => $transaction->is_subscription,
                'subscription_details' => [
                    'start_date' => optional($transaction->subscription_start_date)->format('Y-m-d H:i:s'),
                    'end_date' => optional($transaction->subscription_end_date)->format('Y-m-d H:i:s'),
                    'billing_cycle' => $transaction->billing_cycle,
                ],
                'source' => $transaction->source ? [
                    'id' => $transaction->source->id,
                    'type' => class_basename($transaction->source_type),
                    'details' => $transaction->source->toArray(),
                ] : null,
            ];

            return view('Heights.Admin.Finance.show', [
                'transaction' => $mappedTransaction,
            ]);

        } catch (\Exception $e) {
            Log::error('Admin transaction detail fetch failed: ' . $e->getMessage());
            return redirect()->route('admin.finance.index')->with('error', 'Transaction not found.');
        }
    }


    //
    public function latestOrganizationTransactions(Request $request)
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

    public function latestPlatformOrganizationTransactions(Request $request, string $organization_id)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');

        if (!$user->is_super_admin) {
            return response()->json(['error' => 'This action is only for super admins.'], 403);
        }

        try {
            $transactions = Transaction::where(function ($query) use ($organization_id) {
                $query->where(function ($q) use ($organization_id) {
                    $q->where('seller_type', 'platform')
                        ->where('buyer_type', 'organization')
                        ->where('buyer_id', $organization_id);
                })->orWhere(function ($q) use ($organization_id) {
                    $q->where('buyer_type', 'platform')
                        ->where('seller_type', 'organization')
                        ->where('seller_id', $organization_id);
                });
            })
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();

            $history = $transactions->map(function ($txn) {
                $isPlatformBuyer = $txn->buyer_type === 'platform';

                return [
                    'id' => $txn->id,
                    'title' => $txn->transaction_title,
                    'type' => $isPlatformBuyer ? $txn->buyer_transaction_type : $txn->seller_transaction_type,
                    'price' => number_format($txn->price, 2) . ' ' . $txn->currency,
                    'status' => $txn->status,
                    'created_at' => $txn->created_at->diffForHumans(),
                ];
            });

            return response()->json(['history' => $history]);

        } catch (\Exception $e) {
            Log::error('Admin Transaction history fetch failed: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching transaction history.'], 500);
        }
    }

}
