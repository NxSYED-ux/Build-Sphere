<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class FinanceService
{
    public function getRecentBuildingTransactions(string $organization_id, $buildingIds)
    {
        $transactions = Transaction::whereIn('building_id', $buildingIds)
            ->where(function ($query) use ($organization_id) {
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

        return $this->formatTransactionHistory($transactions, function ($txn) use ($organization_id) {
            return $txn->buyer_type === 'organization' && $txn->buyer_id == $organization_id;
        });
    }

    public function formatTransactionHistory( LengthAwarePaginator|Collection $transactions, callable $buyerCheckCallback = null)
    {
        $items = $transactions instanceof LengthAwarePaginator ? $transactions->items() : $transactions;
        $collection = collect($items)->map(function ($txn) use ($buyerCheckCallback) {
            $useBuyerType = $buyerCheckCallback ? call_user_func($buyerCheckCallback, $txn) : false;

            return [
                'id' => $txn->id,
                'title' => $txn->transaction_title,
                'type' => $useBuyerType ? $txn->buyer_transaction_type : $txn->seller_transaction_type,
                'price' => number_format($txn->price, 2) . ' ' . $txn->currency,
                'status' => $txn->status,
                'payment_method' => $txn->payment_method,
                'created_at' => $txn->created_at->diffForHumans(),
            ];
        });
        return $collection;
    }

    public function prepareFinancialMetrics(float $currentRevenue, float $previousRevenue, float $currentExpenses, float $previousExpenses, float $currentProfit, float $selectedMonth, float $selectedYear): array
    {
        $previousProfit = $previousRevenue - $previousExpenses;

        $revenueChange = $this->calculatePercentageChange($currentRevenue, $previousRevenue);
        $expensesChange = $this->calculatePercentageChange($currentExpenses, $previousExpenses);
        $profitChange = $this->calculatePercentageChange($currentProfit, $previousProfit);

        $financialMetrics = [
            'total_revenue' => [
                'value' => $currentRevenue,
                'change' => $revenueChange,
                'trend' => $revenueChange >= 0 ? 'up' : 'down'
            ],
            'total_expenses' => [
                'value' => $currentExpenses,
                'change' => $expensesChange,
                'trend' => $expensesChange <= 0 ? 'down' : 'up'
            ],
            'net_profit' => [
                'value' => $currentProfit < 0 ? '(' . number_format(abs($currentProfit)) . ')' : $currentProfit,
                'change' => $profitChange,
                'trend' => $profitChange >= 0 ? 'up' : 'down'
            ]
        ];

        return [
            'financialMetrics' => $financialMetrics,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear
        ];
    }

    public function mapTransactionDetails($transaction, bool $isBuyer): array
    {
        return [
            'transaction_id' => $transaction->id,
            'title' => $transaction->transaction_title,
            'type' => $isBuyer ? $transaction->buyer_transaction_type : $transaction->seller_transaction_type,
            'price' => number_format($transaction->price, 2) . ' ' . $transaction->currency,
            'status' => $transaction->status,
            'created_at' => $transaction->created_at->diffForHumans(),
            'payment_method' => $transaction->payment_method,
            'is_subscription' => $transaction->is_subscription,
            'subscription_details' => [
                'start_date' => optional($transaction->subscription_start_date)?->format('Y-m-d H:i:s'),
                'end_date' => optional($transaction->subscription_end_date)?->format('Y-m-d H:i:s'),
                'billing_cycle' => $transaction->billing_cycle,
            ],
        ];
    }


    public function getDateRange(?string $start = null, ?string $end = null, int $days = 30): array
    {
        if ($start && $end) {
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate = Carbon::parse($end)->endOfDay();
        } else {
            $endDate = now()->endOfDay();
            $startDate = now()->subDays(($days - 1))->startOfDay();
        }

        return [$startDate, $endDate];
    }

    public function initializeFinancialChartSkeleton(): array
    {
        return [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => [],
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                ],
                [
                    'label' => 'Expenses',
                    'data' => [],
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                ],
                [
                    'label' => 'Profit',
                    'data' => [],
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                ],
            ],
        ];
    }


    // Helper function
    private function calculatePercentageChange($currentValue, $previousValue)
    {
        if ($previousValue == 0) {
            return $currentValue > 0 ? 100 : ($currentValue < 0 ? -100 : 0);
        }
        return (($currentValue - $previousValue) / abs($previousValue)) * 100;
    }

}
