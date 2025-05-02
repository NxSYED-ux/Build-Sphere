<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\ManagerBuilding;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\UserBuildingUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


class FinanceController extends Controller
{
    // Index
    public function ownerIndex(Request $request)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');
        $token = $request->attributes->get('token');

        if (!$token || empty($token['organization_id']) || empty($token['role_name'])) {
            return redirect()->back()->with('error', 'This info is for Organization owners');
        }

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];

        try {
            $transactionsQuery = Transaction::where(function ($query) use ($organization_id) {
                $query->where('buyer_type', 'organization')->where('buyer_id', $organization_id)
                    ->orWhere('seller_type', 'organization')->where('seller_id', $organization_id);
            });

            $buildingsQuery = Building::where('organization_id', $organization_id);

            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
                $transactionsQuery->whereIn('building_id', $managerBuildingIds);
                $buildingsQuery->whereIn('id', $managerBuildingIds);
            }

            $buildings = $buildingsQuery->get();

            // Type means Debit and Credit
            if ($request->filled('type')) {
                $type = $request->input('type');
                $transactionsQuery->where(function ($q) use ($type, $organization_id) {
                    $q->where(function ($inner) use ($type, $organization_id) {
                        $inner->where('buyer_type', 'organization')
                            ->where('buyer_id', $organization_id)
                            ->where('buyer_transaction_type', $type);
                    })->orWhere(function ($inner) use ($type, $organization_id) {
                        $inner->where('seller_type', 'organization')
                            ->where('seller_id', $organization_id)
                            ->where('seller_transaction_type', $type);
                    });
                });
            }

            // Building Filter.
            if ($request->filled('building_id')) {
                $transactionsQuery->where('building_id', $request->input('building_id'));
            }

            $transactions = $transactionsQuery->filterTransactions($request)
                ->orderBy('created_at', 'desc')
                ->paginate(12)
                ->appends($request->query());

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

            return view('Heights.Owner.Finance.index', compact('transactions', 'history', 'buildings'));

        } catch (\Exception $e) {
            Log::error('Transaction history fetch failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching transaction history.');
        }
    }

    public function adminIndex(Request $request)
    {
        try {
            $transactionsQuery = Transaction::where(function ($query) {
                $query->where('seller_type', 'platform')
                    ->orWhere('buyer_type', 'platform');
            });

            // Type means Debit and Credit
            if ($request->filled('type')) {
                $type = $request->input('type');
                $transactionsQuery->where(function ($q) use ($type) {
                    $q->where(function ($inner) use ($type) {
                        $inner->where('buyer_type', 'platform')
                            ->where('buyer_transaction_type', $type);
                    })->orWhere(function ($inner) use ($type) {
                        $inner->where('seller_type', 'platform')
                            ->where('seller_transaction_type', $type);
                    });
                });
            }

            $transactions = $transactionsQuery->filterTransactions($request)
                ->orderBy('created_at', 'desc')
                ->paginate(12)
                ->appends($request->query());

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

        if (!$token || empty($token['organization_id']) || empty($token['role_name'])) {
            return redirect()->back()->with('error', 'This info is for Organization owners');
        }

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];

        try {
            $transaction = Transaction::findOrFail($id);

            $isOrgBuyer = $transaction->buyer_type === 'organization' && $transaction->buyer_id == $organization_id;
            $isOrgSeller = $transaction->seller_type === 'organization' && $transaction->seller_id == $organization_id;

            if (!($isOrgBuyer || $isOrgSeller)) {
                return redirect()->route('owner.finance.index')->with('error', 'Unauthorized access to transaction details.');
            }

            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $request->user()->id)->pluck('building_id')->toArray();

                if (!in_array($transaction->building_id, $managerBuildingIds)) {
                    return redirect()->route('owner.finance.index')->with('error', 'Unauthorized access to this building\'s transaction.');
                }
            }

            $source = null;
            $nested_source = null;

            if ($transaction->source_name === 'unit contract') {
                $source = UserBuildingUnit::with(['unit', 'unit.pictures', 'user:id,name,picture,email'])
                    ->find($transaction->source_id);
            } elseif ($transaction->source_name === 'subscription') {
                $source = Subscription::find($transaction->source_id);

                if ($source && $source->source_name === 'plan') {
                    $nested_source = Plan::find($source->source_id);
                } elseif ($source) {
                    $nested_source = UserBuildingUnit::with(['unit', 'unit.pictures', 'user:id,name,picture,email'])
                        ->find($source->source_id);
                }
            }

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
            ];

            return view('Heights.Owner.Finance.show', [
                'transaction' => $mappedTransaction,
                'source' => $source,
                'source_name' => $transaction?->source_name,
                'nested_source' => $nested_source,
                'nested_source_name' => $source?->source_name ?? null,
            ]);

        } catch (\Throwable $e) {
            Log::error('Transaction detail fetch failed: ' . $e->getMessage());
            return redirect()->route('owner.finance.index')->with('error', 'Transaction not found.');
        }
    }

    public function adminShow($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);

            $isPlatformBuyer = $transaction->buyer_type === 'platform';
            $isPlatformSeller = $transaction->seller_type === 'platform';

            if (!($isPlatformBuyer || $isPlatformSeller)) {
                return redirect()->route('admin.finance.index')->with('error', 'Unauthorized access to transaction details.');
            }

            $source = null;
            $nested_source = null;

            if ($transaction->source_name === 'subscription') {
                $source = Subscription::find($transaction->source_id);

                if ($source && $source->source_name === 'plan') {
                    $nested_source = Plan::find($source->source_id);
                }
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
                    'start_date' => optional($transaction->subscription_start_date)?->format('Y-m-d H:i:s'),
                    'end_date' => optional($transaction->subscription_end_date)?->format('Y-m-d H:i:s'),
                    'billing_cycle' => $transaction->billing_cycle,
                ],
            ];

            return view('Heights.Admin.Finance.show', [
                'transaction' => $mappedTransaction,
                'source' => $source,
                'source_name' => $transaction->source_name,
                'nested_source' => $nested_source,
                'nested_source_name' => $source?->source_name ?? null,
            ]);

        } catch (\Throwable $e) {
            Log::error('Transaction detail fetch failed: ' . $e->getMessage());
            return redirect()->route('admin.finance.index')->with('error', 'Transaction not found.');
        }
    }


    // Latest organizations
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
                ->limit(4)
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


    // Financial Trends
    public function adminFinancialTrends(Request $request): JsonResponse
    {
        $selectedMonth = (int) $request->input('month', now()->month);
        $selectedYear = (int) $request->input('year', now()->year);

        try {
            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $financialQuery = Transaction::whereBetween('created_at', [$startDate, $endDate]);

            // Current Period
            $currentRevenue = $financialQuery->clone()
                ->where('seller_type', 'platform')
                ->where('status', 'Completed')
                ->sum('price');

            $currentExpenses = $financialQuery->clone()
                ->where('buyer_type', 'platform')
                ->where('status', 'Completed')
                ->sum('price');

            $currentProfit = $currentRevenue - $currentExpenses;

            // Previous Period
            $previousStartDate = $startDate->copy()->subMonth()->startOfMonth();
            $previousEndDate = $startDate->copy()->subMonth()->endOfMonth();

            $previousRevenue = Transaction::whereBetween('created_at', [$previousStartDate, $previousEndDate])
                ->where('seller_type', 'platform')
                ->where('status', 'Completed')
                ->sum('price');

            $previousExpenses = Transaction::whereBetween('created_at', [$previousStartDate, $previousEndDate])
                ->where('buyer_type', 'platform')
                ->where('status', 'Completed')
                ->sum('price');

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
                    'value' => $currentProfit,
                    'change' => $profitChange,
                    'trend' => $profitChange >= 0 ? 'up' : 'down'
                ]
            ];

            return response()->json([
                'financialMetrics' => $financialMetrics,
                'selectedMonth' => $selectedMonth,
                'selectedYear' => $selectedYear
            ], 200);

        } catch (\Exception $e) {
            Log::error('Transaction history fetch failed (Admin): ' . $e->getMessage());
            return response()->json(['error', 'An error occurred while getting the trends.'], 500);
        }
    }

    public function ownerFinancialTrends(Request $request): JsonResponse
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');
        $token = $request->attributes->get('token');
        $selectedMonth = (int) $request->input('month', now()->month);
        $selectedYear = (int) $request->input('year', now()->year);

        if (!$token || empty($token['organization_id']) || empty($token['role_name'])) {
            return response()->json(['error', 'This info is for Organization owners'], 400);
        }

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];

        try {
            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $financialQuery = Transaction::whereBetween('created_at', [$startDate, $endDate]);

            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
                $financialQuery->whereIn('building_id', $managerBuildingIds);
            }

            // Current Period
            $currentRevenue = $financialQuery->clone()
                ->where('seller_type', 'organization')
                ->where('seller_id', $organization_id)
                ->where('status', 'Completed')
                ->sum('price');

            $currentExpenses = $financialQuery->clone()
                ->where('buyer_type', 'organization')
                ->where('buyer_id', $organization_id)
                ->where('status', 'Completed')
                ->sum('price');

            $currentProfit = $currentRevenue - $currentExpenses;

            // Previous Period
            $previousStartDate = $startDate->copy()->subMonth()->startOfMonth();
            $previousEndDate = $startDate->copy()->subMonth()->endOfMonth();

            $previousRevenue = Transaction::whereBetween('created_at', [$previousStartDate, $previousEndDate])
                ->where('seller_type', 'organization')
                ->where('seller_id', $organization_id)
                ->where('status', 'Completed')
                ->sum('price');

            $previousExpenses = Transaction::whereBetween('created_at', [$previousStartDate, $previousEndDate])
                ->where('buyer_type', 'organization')
                ->where('buyer_id', $organization_id)
                ->where('status', 'Completed')
                ->sum('price');

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
                    'value' => $currentProfit,
                    'change' => $profitChange,
                    'trend' => $profitChange >= 0 ? 'up' : 'down'
                ]
            ];

            return response()->json([
                'financialMetrics' => $financialMetrics,
                'selectedMonth' => $selectedMonth,
                'selectedYear' => $selectedYear
            ], 200);

        } catch (\Exception $e) {
            Log::error('Transaction history fetch failed (Owner): ' . $e->getMessage());
            return response()->json(['error', 'An error occurred while getting the trends.'], 500);
        }
    }


    // Chart data
    public function adminFinancialChartData(Request $request)
    {
        try {
            $days = (int) $request->input('days', 30);
            $endDate = now()->endOfDay();
            $startDate = now()->subDays($days)->startOfDay();

            $chartData = [
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
                    ]
                ]
            ];

            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $chartData['labels'][] = $currentDate->format('M d');

                $revenue = Transaction::whereDate('created_at', $currentDate)
                    ->where('seller_type', 'platform')
                    ->where('status', 'Completed')
                    ->sum('price');

                $expenses = Transaction::whereDate('created_at', $currentDate)
                    ->where('buyer_type', 'platform')
                    ->where('status', 'Completed')
                    ->sum('price');

                $chartData['datasets'][0]['data'][] = $revenue;
                $chartData['datasets'][1]['data'][] = $expenses;
                $chartData['datasets'][2]['data'][] = $revenue - $expenses;

                $currentDate->addDay();
            }

            return response()->json($chartData);

        } catch (\Exception $e) {
            Log::error('Financial chart data failed (Admin) : ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load chart data'], 500);
        }
    }

    public function ownerFinancialChartData(Request $request)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');
        $token = $request->attributes->get('token');

        if (!$token || empty($token['organization_id']) || empty($token['role_name'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];
        $managerBuildingIds = null;

        try {
            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)
                    ->pluck('building_id')
                    ->toArray();
            }

            $days = (int) $request->input('days', 30);
            $endDate = now()->endOfDay();
            $startDate = now()->subDays($days)->startOfDay();

            $chartData = [
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
                    ]
                ]
            ];

            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $chartData['labels'][] = $currentDate->format('M d');

                $revenue = Transaction::whereDate('created_at', $currentDate)
                    ->where('seller_type', 'organization')
                    ->where('seller_id', $organization_id)
                    ->where('status', 'Completed')
                    ->when($role_name === 'Manager', function($query) use ($managerBuildingIds) {
                        return $query->whereIn('building_id', $managerBuildingIds);
                    })
                    ->sum('price');

                $expenses = Transaction::whereDate('created_at', $currentDate)
                    ->where('buyer_type', 'organization')
                    ->where('buyer_id', $organization_id)
                    ->where('status', 'Completed')
                    ->when($role_name === 'Manager', function($query) use ($managerBuildingIds) {
                        return $query->whereIn('building_id', $managerBuildingIds);
                    })
                    ->sum('price');

                $chartData['datasets'][0]['data'][] = $revenue;
                $chartData['datasets'][1]['data'][] = $expenses;
                $chartData['datasets'][2]['data'][] = $revenue - $expenses;

                $currentDate->addDay();
            }

            return response()->json($chartData);

        } catch (\Exception $e) {
            Log::error('Financial chart data failed (Owner): ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load chart data'], 500);
        }
    }



    // Helper Function
    private function calculatePercentageChange($currentValue, $previousValue)
    {
        if ($previousValue == 0) {
            return $currentValue > 0 ? 100 : ($currentValue < 0 ? -100 : 0);
        }
        return (($currentValue - $previousValue) / abs($previousValue)) * 100;
    }

}
