<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\FinanceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;


class TransactionController extends Controller
{
    public function index()
    {
        try {
            $user = request()->user();

            $transactions = Transaction::where(function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('buyer_type', 'user')->where('buyer_id', $user->id);
                })->orWhere(function ($q) use ($user) {
                    $q->where('seller_type', 'user')->where('seller_id', $user->id);
                });
            })
                ->orderBy('created_at', 'desc')
                ->get();

            $financeService = new FinanceService();
            $history = $financeService->formatTransactionHistory($transactions, function ($txn) use ($user) {
                return $txn->buyer_type === 'user' && $txn->buyer_id === $user->id;
            });

            return response()->json(['history' => $history]);

        } catch (\Throwable $e) {
            Log::error('Transaction history fetch failed: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching transaction history.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = request()->user();

            $transaction = Transaction::where(function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('buyer_type', 'user')->where('buyer_id', $user->id);
                })->orWhere(function ($q) use ($user) {
                    $q->where('seller_type', 'user')->where('seller_id', $user->id);
                });
            })->findOrFail($id);


            $isBuyer = $transaction->buyer_type === 'user' && $transaction->buyer_id === $user->id;
            $isSeller = $transaction->seller_type === 'user' && $transaction->seller_id === $user->id;

            if (!($isBuyer || $isSeller)) {
                return redirect()->back()->with('error', 'Unauthorized access to transaction details.');
            }

            $financeService = new FinanceService();
            $transactionDetail = $financeService->mapTransactionDetails($transaction, $isBuyer);

            return response()->json(['transaction' => $transactionDetail]);

        } catch (ModelNotFoundException $e) {
            Log::error('Transaction details fetch failed: ' . $e->getMessage());
            return response()->json(['error' => 'Transaction not found.'], 404);
        } catch (\Throwable $e) {
            Log::error('Transaction details fetch failed: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching transaction details.'], 500);
        }
    }

}
