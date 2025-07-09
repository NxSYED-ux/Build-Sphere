<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\ManagerBuilding;
use App\Models\Membership;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\UserBuildingUnit;
use App\Services\AdminFiltersService;
use App\Services\FinanceService;
use App\Services\OwnerFiltersService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


class FinanceController extends Controller
{
    // Index
    public function ownerIndex(Request $request)
    {
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        $type = $request->input('type');
        $selectedBuildingId = $request->input('building_id');
        $selectedUnitId = $request->input('unit_id');
        $selectedMembershipId = $request->input('membership_id');
        $selectedUserId = $request->input('user_id');


        try {
            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();
            $buildings = $ownerService->buildings($buildingIds);
            $units  = $ownerService->units($buildingIds);
            $users = $ownerService->users(false);
            $memberships = $ownerService->memberships($buildingIds);


            $transactionsQuery = Transaction::where(function ($query) use ($organization_id) {
                    $query->where('buyer_type', 'organization')->where('buyer_id', $organization_id)
                        ->orWhere('seller_type', 'organization')->where('seller_id', $organization_id);
                });

            if(!(request()->user()->id === 2)){
                $transactionsQuery->whereIn('building_id', $buildingIds);
            }

            if ($type) {
                $transactionsQuery->where(function ($q) use ($type, $organization_id) {
                    $q->where(function ($inner) use ($type, $organization_id) {
                        $inner->where('buyer_type', 'organization')
                            ->where('buyer_id', $organization_id);
                    })->orWhere(function ($inner) use ($type, $organization_id) {
                        $inner->where('seller_type', 'organization')
                            ->where('seller_id', $organization_id)
                            ->where('seller_transaction_type', $type);
                    });
                });
            }

            if ($selectedUserId) {
                $transactionsQuery->where(function ($q) use ($selectedUserId) {
                    $q->where(function ($inner) use ($selectedUserId) {
                        $inner->where('buyer_id', $selectedUserId)
                            ->where('buyer_type', 'user');
                    })->orWhere(function ($inner) use ($selectedUserId) {
                        $inner->where('seller_id', $selectedUserId)
                            ->where('seller_type', 'user');
                    });
                });
            }

            if ($selectedBuildingId) {
                $transactionsQuery->where('building_id', $selectedBuildingId);
            }

            if ($selectedUnitId) {
                $transactionsQuery->where('unit_id', $selectedUnitId);
            }

            if ($selectedMembershipId) {
                $transactionsQuery->where('membership_id', $selectedMembershipId);
            }

            $transactions = $transactionsQuery->filterTransactions($request)
                ->orderBy('created_at', 'desc')
                ->paginate(12)
                ->appends($request->query());

            $financeService = new FinanceService();
            $history = $financeService->formatTransactionHistory($transactions, function ($txn) use ($organization_id) {
                return $txn->buyer_type === 'organization' && $txn->buyer_id == $organization_id;
            });

            return view('Heights.Owner.Finance.index', compact('transactions', 'history', 'buildings', 'units', 'memberships', 'users'));

        } catch (\Exception $e) {
            Log::error('Transaction history fetch failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching transaction history.');
        }
    }

    public function adminIndex(Request $request)
    {
        try {
            $adminService = new AdminFiltersService();
            $organizations = $adminService->organizations();
            $plans = $adminService->plans();

            $type = $request->input('type');
            $selectedOrganizationId = $request->input('organization_id');
            $selectedPlanId = request()->input('plan_id');

            $transactionsQuery = Transaction::where(function ($query) {
                $query->where('seller_type', 'platform')
                    ->orWhere('buyer_type', 'platform');
            });

            if ($type) {
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

            if ($selectedOrganizationId) {
                $transactionsQuery->where(function ($q) use ($selectedOrganizationId) {
                    $q->where(function ($inner) use ($selectedOrganizationId) {
                        $inner->where('buyer_id', $selectedOrganizationId)
                            ->where('buyer_type', 'organization');
                    })->orWhere(function ($inner) use ($selectedOrganizationId) {
                        $inner->where('seller_id', $selectedOrganizationId)
                            ->where('seller_type', 'organization');
                    });
                });
            }

            if ($selectedPlanId) {
                $transactionsQuery->where('plan_id', $selectedPlanId);
            }

            $transactions = $transactionsQuery->filterTransactions($request)
                ->orderBy('created_at', 'desc')
                ->paginate(12)
                ->appends($request->query());


            $financeService = new FinanceService();
            $history = $financeService->formatTransactionHistory($transactions, function ($txn) {
                return $txn->buyer_type === 'platform';
            });

            return view('Heights.Admin.Finance.index', compact('transactions', 'history', 'plans', 'organizations'));

        } catch (\Exception $e) {
            Log::error('Transaction history fetch failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching transaction history.');
        }
    }


    // Show
    public function ownerShow($id, Request $request)
    {
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        try {
            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();

            $transaction = Transaction::findOrFail($id);

            $isOrgBuyer = $transaction->buyer_type === 'organization' && $transaction->buyer_id == $organization_id;
            $isOrgSeller = $transaction->seller_type === 'organization' && $transaction->seller_id == $organization_id;

            if (!($isOrgBuyer || $isOrgSeller)) {
                return redirect()->back()->with('error', 'Unauthorized access to transaction details.');
            }

            if(!(request()->user()->id === 2)){
                if (!in_array($transaction->building_id, $buildingIds)) {
                    return redirect()->back()->with('error', 'Unauthorized access to this building\'s transaction.');
                }
            }

            $source = null;
            $nested_source = null;

            if ($transaction->source_name === 'unit contract') {
                $source = UserBuildingUnit::with(['unit', 'unit.pictures', 'user:id,name,picture,email'])
                    ->find($transaction->source_id);

            }
            elseif ($transaction->source_name === 'subscription') {
                $source = Subscription::find($transaction->source_id);

                if ($source && $source->source_name === 'plan') {
                    $nested_source = Plan::find($source->source_id);
                }
                elseif ($source && $source->source_name === 'unit contract') {
                    $nested_source = UserBuildingUnit::with(['unit', 'unit.pictures', 'user:id,name,picture,email'])
                        ->find($source->source_id);
                }
                elseif ($source && $source->source_name === 'membership') {
                    $nested_source = Membership::find($source->source_id);
                }
            }

            $financeService = new FinanceService();
            $mappedTransaction = $financeService->mapTransactionDetails($transaction, $isOrgBuyer);

            return view('Heights.Owner.Finance.show', [
                'transaction' => $mappedTransaction,
                'source' => $source,
                'source_name' => $transaction?->source_name,
                'nested_source' => $nested_source,
                'nested_source_name' => $source?->source_name ?? null,
            ]);

        } catch (\Throwable $e) {
            Log::error('Transaction detail fetch failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Transaction not found.');
        }
    }

    public function adminShow($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);

            $isPlatformBuyer = $transaction->buyer_type === 'platform';
            $isPlatformSeller = $transaction->seller_type === 'platform';

            if (!($isPlatformBuyer || $isPlatformSeller)) {
                return redirect()->back()->with('error', 'Unauthorized access to transaction details.');
            }

            $source = null;
            $nested_source = null;

            if ($transaction->source_name === 'subscription') {
                $source = Subscription::find($transaction->source_id);

                if ($source && $source->source_name === 'plan') {
                    $plan = Plan::find($source->source_id);
                    $organization = Organization::find($source->organization_id);
                    $nested_source = [
                        'plan' => $plan,
                        'organization' => $organization,
                    ];
                }
            }

            $financeService = new FinanceService();
            $mappedTransaction = $financeService->mapTransactionDetails($transaction, $isPlatformBuyer);

            return view('Heights.Admin.Finance.show', [
                'transaction' => $mappedTransaction,
                'source' => $source,
                'source_name' => $transaction->source_name,
                'nested_source' => $nested_source,
                'nested_source_name' => $source?->source_name ?? null,
            ]);

        } catch (\Throwable $e) {
            Log::error('Transaction detail fetch failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Transaction not found.');
        }
    }


    // Latest organizations
    public function latestOrganizationTransactions(Request $request)
    {
        $token = $request->attributes->get('token');
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

            $financeService = new FinanceService();
            $history = $financeService->formatTransactionHistory($transactions, function ($txn) use ($organization_id) {
                return $txn->buyer_type === 'organization' && $txn->buyer_id == $organization_id;
            });

            return response()->json(['history' => $history]);

        } catch (\Exception $e) {
            Log::error('Transaction history fetch failed: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching transaction history.'], 500);
        }
    }

    public function latestPlatformOrganizationTransactions(string $organization_id)
    {
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

            $financeService = new FinanceService();
            $history = $financeService->formatTransactionHistory($transactions, function ($txn) {
                return $txn->buyer_type === 'platform';
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

            $financeService = new FinanceService();
            $metrics = $financeService->prepareFinancialMetrics(
                $currentRevenue,
                $previousRevenue,
                $currentExpenses,
                $previousExpenses,
                $currentProfit,
                $selectedMonth,
                $selectedYear
            );

            return response()->json($metrics, 200);

        } catch (\Exception $e) {
            Log::error('Transaction history fetch failed (Admin): ' . $e->getMessage());
            return response()->json(['error', 'An error occurred while getting the trends.'], 500);
        }
    }

    public function ownerFinancialTrends(Request $request): JsonResponse
    {
        $selectedMonth = (int) $request->input('month', now()->month);
        $selectedYear = (int) $request->input('year', now()->year);

        try {
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $financialQuery = Transaction::whereBetween('created_at', [$startDate, $endDate]);

            if (request()->user()->id !== 2) {
                $ownerService = new OwnerFiltersService();
                $buildingIds = $ownerService->getAccessibleBuildingIds();
                $financialQuery->whereIn('building_id', $buildingIds);
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

            $previousQuery = Transaction::whereBetween('created_at', [$previousStartDate, $previousEndDate]);

            if (request()->user()->id !== 2) {
                $previousQuery->whereIn('building_id', $buildingIds);
            }

            $previousRevenue = $previousQuery->clone()
                ->where('seller_type', 'organization')
                ->where('seller_id', $organization_id)
                ->where('status', 'Completed')
                ->sum('price');

            $previousExpenses = $previousQuery->clone()
                ->where('buyer_type', 'organization')
                ->where('buyer_id', $organization_id)
                ->where('status', 'Completed')
                ->sum('price');

            $financeService = new FinanceService();
            $metrics = $financeService->prepareFinancialMetrics(
                $currentRevenue,
                $previousRevenue,
                $currentExpenses,
                $previousExpenses,
                $currentProfit,
                $selectedMonth,
                $selectedYear
            );

            return response()->json($metrics, 200);

        } catch (\Exception $e) {
            Log::error('Transaction history fetch failed (Owner): ' . $e->getMessage());
            return response()->json(['error', 'An error occurred while getting the trends.'], 500);
        }
    }


    // Chart data
    public function adminFinancialChartData(Request $request)
    {
        try {
            $financialService = new FinanceService();
            $chartData = $financialService->initializeFinancialChartSkeleton();
            [$startDate, $endDate] = $financialService->getDateRange(
                $request->input('start_date'),
                $request->input('end_date'),
                $request->input('days', 30)
            );

            $totalDays = (int) $startDate->diffInDays($endDate) + 1;
            $segments = min($totalDays, 20);
            $daysPerSegment = max(1, ceil($totalDays / $segments));

            $segmentStart = $startDate->copy();
            while ($segmentStart->lte($endDate)) {
                $segmentEnd = $segmentStart->copy()->addDays($daysPerSegment - 1)->endOfDay();
                if ($segmentEnd->gt($endDate)) {
                    $segmentEnd = $endDate->copy()->endOfDay();
                }

                $label = $segmentStart->isSameDay($segmentEnd)
                    ? $segmentStart->format('d M')
                    : $segmentStart->format('d M') . ' - ' . $segmentEnd->format('d M');
                $chartData['labels'][] = $label;

                $revenue = Transaction::whereBetween('created_at', [$segmentStart, $segmentEnd])
                    ->where('seller_type', 'platform')
                    ->where('status', 'Completed')
                    ->sum('price');

                $expense = Transaction::whereBetween('created_at', [$segmentStart, $segmentEnd])
                    ->where('buyer_type', 'platform')
                    ->where('status', 'Completed')
                    ->sum('price');

                $chartData['datasets'][0]['data'][] = round($revenue, 2);
                $chartData['datasets'][1]['data'][] = round($expense, 2);
                $chartData['datasets'][2]['data'][] = round($revenue - $expense, 2);

                $segmentStart = $segmentEnd->copy()->addSecond();
            }

            return response()->json($chartData);

        } catch (\Throwable $e) {
            Log::error('Financial chart data failed (Admin) : ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load chart data'], 500);
        }
    }

    public function ownerFinancialChartData(Request $request)
    {
        $isRestrictedToBuildings = request()->user()->id !== 2;
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];
        $buildingIds = [];

        $building_id = $request->input('building');
        $unit_id = $request->input('unit');
        $membership_id = $request->input('membership');
        $user_id = $request->input('user_id');
        $max_segments = (int) $request->input('segments', 20);

        try {
            if ($isRestrictedToBuildings) {
                $ownerService = new OwnerFiltersService();
                $buildingIds = $ownerService->getAccessibleBuildingIds();
            }

            $financialService = new FinanceService();
            $chartData = $financialService->initializeFinancialChartSkeleton();

            [$startDate, $endDate] = $financialService->getDateRange(
                $request->input('start_date'),
                $request->input('end_date'),
                $request->input('days', 30)
            );

            $totalDays = (int) $startDate->diffInDays($endDate) + 1;
            $segments = min($totalDays, $max_segments);
            $daysPerSegment = max(1, ceil($totalDays / $segments));

            $segmentStart = $startDate->copy();

            while ($segmentStart->lte($endDate)) {
                $segmentEnd = $segmentStart->copy()->addDays($daysPerSegment - 1)->endOfDay();
                if ($segmentEnd->gt($endDate)) {
                    $segmentEnd = $endDate->copy()->endOfDay();
                }

                $label = $segmentStart->isSameDay($segmentEnd)
                    ? $segmentStart->format('d M')
                    : $segmentStart->format('d M') . ' - ' . $segmentEnd->format('d M');
                $chartData['labels'][] = $label;

                $revenueQuery = Transaction::whereBetween('created_at', [$segmentStart, $segmentEnd])
                    ->where('seller_type', 'organization')
                    ->where('seller_id', $organization_id)
                    ->where('status', 'Completed');

                $expenseQuery = Transaction::whereBetween('created_at', [$segmentStart, $segmentEnd])
                    ->where('buyer_type', 'organization')
                    ->where('buyer_id', $organization_id)
                    ->where('status', 'Completed');

                if ($isRestrictedToBuildings) {
                    $revenueQuery->whereIn('building_id', $buildingIds);
                    $expenseQuery->whereIn('building_id', $buildingIds);
                }

                if ($user_id) {
                    $revenueQuery->where(function ($q) use ($user_id) {
                        $q->where('buyer_type', 'user')->where('buyer_id', $user_id);
                    });
                    $expenseQuery->where(function ($q) use ($user_id) {
                        $q->where('seller_type', 'user')->where('seller_id', $user_id);
                    });
                }

                if ($building_id) {
                    $revenueQuery->where('building_id', $building_id);
                    $expenseQuery->where('building_id', $building_id);
                }

                if ($unit_id) {
                    $revenueQuery->where('unit_id', $unit_id);
                    $expenseQuery->where('unit_id', $unit_id);
                }

                if ($membership_id) {
                    $revenueQuery->where('membership_id', $membership_id);
                    $expenseQuery->where('membership_id', $membership_id);
                }

                $revenue = $revenueQuery->sum('price');
                $expense = $expenseQuery->sum('price');

                $chartData['datasets'][0]['data'][] = round($revenue, 2);
                $chartData['datasets'][1]['data'][] = round($expense, 2);
                $chartData['datasets'][2]['data'][] = round($revenue - $expense, 2);

                $segmentStart = $segmentEnd->copy()->addSecond();
            }

            Log::info('Chart Data' . json_encode($chartData));

            return response()->json($chartData);

        } catch (\Throwable $e) {
            Log::error('Financial chart data failed (Owner): ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load chart data'], 500);
        }
    }



    // Monthly Finance Overview
    public function getOrgMonthlyFinancialStats(Request $request)
    {
        $user = $request->user();
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];
        $role_id = $token['role_id'];

        return $this->orgMonthlyStats($request, $organization_id, $role_id, $user->id, 'user_id');
    }

    // Monthly Finance Overview for Manager Detail Page
    public function getManagerBuildingsMonthlyStats(Request $request, string $id)
    {
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        return $this->orgMonthlyStats($request, $organization_id, 3, $id, 'staff_id');
    }

    // Helper Function
    private function orgMonthlyStats(Request $request, string $organization_id, int $roleId, string $id, string $trackOn)
    {
        $selectedBuildingId = $request->input('buildingId');
        try {

            $buildingIds = [];
            if ($roleId === 3) {
                $buildingIds = ManagerBuilding::where($trackOn, $id)
                    ->pluck('building_id')
                    ->toArray();

                if ($selectedBuildingId && !in_array($selectedBuildingId, $buildingIds)) {
                    return response()->json(['error' => 'Unauthorized building access'], 403);
                }
            }

            $year = $request->input('year', now()->year);

            $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
            $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay();

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
                $monthLabel = $currentDate->format('M');
                $chartData['labels'][] = $monthLabel;

                $revenue = Transaction::whereBetween('created_at', [$currentDate->copy()->startOfMonth(), $currentDate->copy()->endOfMonth()])
                    ->where('seller_type', 'organization')
                    ->where('seller_id', $organization_id)
                    ->where('status', 'Completed')
                    ->when($selectedBuildingId, function ($query) use ($selectedBuildingId) {
                        return $query->where('building_id', $selectedBuildingId);
                    })
                    ->when($roleId === 3 && !$selectedBuildingId, function ($query) use ($buildingIds) {
                        return $query->whereIn('building_id', $buildingIds);
                    })
                    ->sum('price');

                $expenses = Transaction::whereBetween('created_at', [$currentDate->copy()->startOfMonth(), $currentDate->copy()->endOfMonth()])
                    ->where('buyer_type', 'organization')
                    ->where('buyer_id', $organization_id)
                    ->where('status', 'Completed')
                    ->when($selectedBuildingId, function ($query) use ($selectedBuildingId) {
                        return $query->where('building_id', $selectedBuildingId);
                    })
                    ->when($roleId === 3 && !$selectedBuildingId, function ($query) use ($buildingIds) {
                        return $query->whereIn('building_id', $buildingIds);
                    })
                    ->sum('price');

                $chartData['datasets'][0]['data'][] = $revenue;
                $chartData['datasets'][1]['data'][] = $expenses;
                $chartData['datasets'][2]['data'][] = $revenue - $expenses;

                $currentDate->addMonth();
            }

            return response()->json($chartData);

        } catch (\Throwable $e) {
            Log::error('Financial chart data failed (Owner): ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load chart data'], 500);
        }
    }

}
