<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BuildingUnit;
use App\Models\ManagerBuilding;
use App\Models\MembershipUser;
use App\Models\Query;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\UserBuildingUnit;
use App\Services\OwnerFiltersService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{
    public function index()
    {
        try {
            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();
            $buildings = $ownerService->buildings($buildingIds);
            $units = $ownerService->allUnitsExceptMembershipUnits($buildingIds);

            return view('Heights.Owner.Reports.index', compact('buildings', 'units'));
        } catch (\Throwable $e) {
            Log::error('Error in Owner Report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }


    // For both Building and Unit
    public function getFinance(Request $request)
    {
        try {
            $building_id = $request->input('building');
            $unit_id = $request->input('unit');
            $start = $request->input('start') ? Carbon::parse($request->input('start'))->startOfDay() : now()->subDays(29)->startOfDay();
            $end = $request->input('end') ? Carbon::parse($request->input('end'))->endOfDay() : now()->endOfDay();

            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];
            $roleId = $request->user()->role_id;

            $ownerService = new OwnerFiltersService();
            if ($roleId !== 2) {
                $accessibleBuildingIds = $ownerService->getAccessibleBuildingIds();

                if ($building_id && !in_array($building_id, $accessibleBuildingIds)) {
                    return response()->json(['message' => 'You do not have access to the selected building.'], 403);
                }
            }

            $baseQuery = Transaction::whereBetween('created_at', [$start, $end])
                ->where('status', 'Completed');

            $incomeQuery = (clone $baseQuery)
                ->where('seller_type', 'organization')
                ->where('seller_id', $organization_id)
                ->when($roleId !== 2, fn($q) => $q->whereIn('building_id', $accessibleBuildingIds))
                ->when($building_id, fn($q) => $q->where('building_id', $building_id))
                ->when($unit_id, fn($q) => $q->where('unit_id', $unit_id));

            $expenseQuery = (clone $baseQuery)
                ->where('buyer_type', 'organization')
                ->where('buyer_id', $organization_id)
                ->when($roleId !== 2, fn($q) => $q->whereIn('building_id', $accessibleBuildingIds))
                ->when($building_id, fn($q) => $q->where('building_id', $building_id))
                ->when($unit_id, fn($q) => $q->where('unit_id', $unit_id));

            $incomeTransactions = $incomeQuery->with('unit')->get();
            $expenseTransactions = $expenseQuery->with('unit')->get();

            $totalIncome = $incomeTransactions->sum('price');
            $totalExpense = $expenseTransactions->sum('price');
            $netProfit = $totalIncome - $totalExpense;
            $profitMargin = $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 2) : 0;

            $incomeSources = $incomeTransactions->groupBy('transaction_title')->map(fn($g) => $g->sum('price'));
            $expenseSources = $expenseTransactions->groupBy('transaction_title')->map(fn($g) => $g->sum('price'));

            $recentTransactions = collect([...$incomeTransactions, ...$expenseTransactions])
                ->sortByDesc('created_at')
                ->map(function ($t) {
                    return [
                        'id' => 'TX-' . str_pad($t->id, 4, '0', STR_PAD_LEFT),
                        'title' => $t->transaction_title,
                        'unit' => $t->unit?->unit_name ?? 'N/A',
                        'source' => match (true) {
                            $t->source_name === 'unit contract' => 'Sale',
                            ($t->source_name === 'subscription' || $t->source_name === 'membership') && $t->membership_id != null => 'Membership',
                            $t->source_name === 'subscription' && $t->membership_id === null => 'Rent',
                            $t->source_name === 'query' => 'Maintenance Request',
                            default => 'Other',
                        },
                        'type' => $t->seller_type === 'organization' ? 'Income' : 'Expense',
                        'amount' => $t->price,
                        'date' => $t->created_at->format('Y-m-d')
                    ];
                })
                ->values()
                ->toArray();

            $days = $start->diffInDays($end);
            $previousStart = (clone $start)->subDays($days + 1);
            $previousEnd = (clone $start)->subDay();

            $prevIncome = Transaction::where('seller_type', 'organization')
                ->where('seller_id', $organization_id)
                ->where('status', 'Completed')
                ->whereBetween('created_at', [$previousStart, $previousEnd])
                ->when($roleId !== 2, fn($q) => $q->whereIn('building_id', $accessibleBuildingIds))
                ->when($building_id, fn($q) => $q->where('building_id', $building_id))
                ->when($unit_id, fn($q) => $q->where('unit_id', $unit_id))
                ->sum('price');

            $prevExpense = Transaction::where('buyer_type', 'organization')
                ->where('buyer_id', $organization_id)
                ->where('status', 'Completed')
                ->whereBetween('created_at', [$previousStart, $previousEnd])
                ->when($roleId !== 2, fn($q) => $q->whereIn('building_id', $accessibleBuildingIds))
                ->when($building_id, fn($q) => $q->where('building_id', $building_id))
                ->when($unit_id, fn($q) => $q->where('unit_id', $unit_id))
                ->sum('price');

            $incomeGrowth = $prevIncome > 0 ? round((($totalIncome - $prevIncome) / $prevIncome) * 100, 2) : ($totalIncome > 0 ? 100 : 0);
            $expenseGrowth = $prevExpense > 0 ? round((($totalExpense - $prevExpense) / $prevExpense) * 100, 2) : ($totalExpense > 0 ? 100 : 0);

            return response()->json([
                'overview' => [
                    'income' => $totalIncome,
                    'expense' => $totalExpense,
                    'net_profit' => $netProfit,
                    'profit_margin' => $profitMargin,
                ],
                'growth' => [
                    'income' => $incomeGrowth,
                    'expense' => $expenseGrowth,
                ],
                'income_sources' => [
                    'labels' => $incomeSources->keys()->toArray(),
                    'data' => array_values($incomeSources->toArray()),
                    'colors' => ['#184E83', '#1A6FC9', '#2ecc71'],
                ],
                'expense_sources' => [
                    'labels' => $expenseSources->keys()->toArray(),
                    'data' => array_values($expenseSources->toArray()),
                    'colors' => ['#ff4d6d', '#e67e22', '#9b59b6'],
                ],
//                'income_sources' => [
//                    'labels' => $incomeSources->keys()->toArray(),
//                    'data' => array_values($incomeSources->toArray()),
//                    'colors' => ['#184E83', '#1A6FC9', '#2ecc71'],
//                ],
//                'expense_sources' => [
//                    'labels' => $expenseSources->keys()->toArray(),
//                    'data' => array_values($expenseSources->toArray()),
//                    'colors' => ['#ff4d6d', '#e67e22', '#9b59b6'],
//                ],
                'recent_transactions' => $recentTransactions
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in getFinance: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve financial overview.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Building
    public function getBuildingOccupancy(Request $request)
    {
        try {
            $start = $request->input('start') ? Carbon::parse($request->input('start'))->startOfDay() : now()->subDays(29)->startOfDay();
            $end = $request->input('end') ? Carbon::parse($request->input('end'))->endOfDay() : now()->endOfDay();

            $building_id = $request->input('building');
            $ownerService = new OwnerFiltersService();
            $accessibleBuildingIds = $ownerService->getAccessibleBuildingIds();

            if ($building_id && !in_array($building_id, $accessibleBuildingIds)) {
                return response()->json([
                    'message' => 'You do not have access to the selected building.'
                ], 403);
            }

            $buildingIds = $building_id ? [$building_id] : $accessibleBuildingIds;

            $units = BuildingUnit::whereIn('building_id', $buildingIds)
                ->where('sale_or_rent', '!=', 'Not Available')
                ->where('created_at', '<=', $end)
                ->get(['id', 'created_at']);

            $unitIds = $units->pluck('id')->toArray();

            $soldContracts = UserBuildingUnit::whereIn('unit_id', $unitIds)
                ->where('contract_status', 1)
                ->where('type', 'Sold')
                ->get(['unit_id', 'created_at']);

            $subscriptions = Subscription::where('source_name', 'unit contract')
                ->whereIn('unit_id', $unitIds)
                ->whereIn('building_id', $buildingIds)
                ->get(['unit_id', 'created_at', 'ends_at']);

            $totalDays = (int) $start->diffInDays($end) + 1;
            $segments = min($totalDays, 15);
            $daysPerSegment = max(1, ceil($totalDays / $segments));

            $labels = [];
            $availableData = $rentedData = $soldData = [];

            $segmentStart = $start->copy();
            while ($segmentStart->lte($end)) {
                $segmentEnd = $segmentStart->copy()->addDays($daysPerSegment - 1)->endOfDay();
                if ($segmentEnd->gt($end)) {
                    $segmentEnd = $end->copy()->endOfDay();
                }

                $label = $segmentStart->isSameDay($segmentEnd)
                    ? $segmentStart->format('d M')
                    : $segmentStart->format('d M') . ' - ' . $segmentEnd->format('d M');

                $labels[] = $label;

                $segmentUnits = $units->filter(fn($q) => $q->created_at <= $segmentEnd)->pluck('id')->toArray();

                $segmentSold = $soldContracts
                    ->filter(fn($s) => in_array($s->unit_id, $segmentUnits) && $s->created_at <= $segmentEnd)
                    ->pluck('unit_id')
                    ->unique()
                    ->toArray();

                $soldCount = $soldContracts
                    ->filter(fn($s) => in_array($s->unit_id, $segmentUnits) && $s->created_at->between($segmentStart, $segmentEnd))
                    ->pluck('unit_id')
                    ->unique()
                    ->count();

                $segmentRented = $subscriptions
                    ->filter(fn($s) => in_array($s->unit_id, $segmentUnits) && (is_null($s->ends_at) || $s->ends_at >= $segmentStart) && ($s->created_at <= $segmentEnd))
                    ->pluck('unit_id')
                    ->unique()
                    ->toArray();

                $rentedCount = $subscriptions
                    ->filter(fn($s) => in_array($s->unit_id, $segmentUnits) && $s->created_at->between($segmentStart, $segmentEnd))
                    ->pluck('unit_id')
                    ->unique()
                    ->count();

                $occupied = array_unique(array_merge($segmentRented, $segmentSold));
                $available = count($segmentUnits) - count($occupied);

                $availableData[] = max($available, 0);
                $rentedData[] = $rentedCount;
                $soldData[] = $soldCount;

                $segmentStart = $segmentEnd->copy()->addSecond();
            }

            $totalAvailable = max($availableData);
            $totalRented = array_sum($rentedData);
            $totalSold = array_sum($soldData);
            $occupiedUnits = $totalRented + $totalSold;
            $totalUnits = 0;

            if($occupiedUnits > 0){
                $totalUnits = BuildingUnit::whereIn('building_id', $buildingIds)
                    ->where('sale_or_rent', '!=', 'Not Available')
                    ->where('created_at', '<=', $end)
                    ->count();
            }

            $occupancyRate = $totalUnits > 0
                ? round(($occupiedUnits / $totalUnits) * 100, 2)
                : 0;


            return response()->json([
                'occupancyRate' => $occupancyRate,
                'totals' => [
                    'available' => $totalAvailable,
                    'rented' => $totalRented,
                    'sold' => $totalSold
                ],
                'occupancy_trend' => [
                    'labels' => $labels,
                    'available' => $availableData,
                    'rented' => $rentedData,
                    'sold' => $soldData,
                ]
            ]);

        } catch (\Throwable $e) {
            Log::error('Error in Building Report (getOccupancy): ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while preparing the unit occupancy chart.'
            ], 500);
        }
    }

    public function getMembershipsStats(Request $request)
    {
        try {
            $building_id = $request->input('building');
            $membership_id = $request->input('membership');

            $start = $request->input('start')
                ? Carbon::parse($request->input('start'))->startOfDay()
                : now()->subDays(29)->startOfDay();

            $end = $request->input('end')
                ? Carbon::parse($request->input('end'))->endOfDay()
                : now()->endOfDay();

            $ownerService = new OwnerFiltersService();
            $accessibleBuildingIds = $ownerService->getAccessibleBuildingIds();

            if ($building_id && !in_array($building_id, $accessibleBuildingIds)) {
                return response()->json(['message' => 'You do not have access to the selected building.'], 403);
            }

            $buildingIds = $building_id ? [$building_id] : $accessibleBuildingIds;
            $validMemberships = $ownerService->memberships($buildingIds);
            $accessibleMemberships = $validMemberships->pluck('id')->toArray();

            if ($membership_id && !in_array($membership_id, $accessibleMemberships)) {
                return response()->json(['message' => 'You do not have access to the selected membership.'], 403);
            }

            $accessibleMemberships = $membership_id ? [$membership_id] : $accessibleMemberships;

            $totalDays = (int) $start->diffInDays($end) + 1;
            $maxSegments = 15;
            $daysPerSegment = max(1, ceil($totalDays / $maxSegments));

            $labels = [];
            $activeTrend = [];
            $expiredTrend = [];

            $segmentStart = $start->copy();
            while ($segmentStart->lte($end)) {
                $segmentEnd = $segmentStart->copy()->addDays($daysPerSegment - 1)->endOfDay();
                if ($segmentEnd->gt($end)) {
                    $segmentEnd = $end->copy()->endOfDay();
                }

                $label = $segmentStart->isSameDay($segmentEnd)
                    ? $segmentStart->format('d M')
                    : $segmentStart->format('d M') . ' - ' . $segmentEnd->format('d M');

                $labels[] = $label;

                $segmentActive = MembershipUser::whereIn('membership_id', $accessibleMemberships)
                    ->where('ends_at', '>=', $segmentStart)
                    ->where('created_at', '<=', $segmentEnd)
                    ->count();

                $segmentExpired = MembershipUser::whereIn('membership_id', $accessibleMemberships)
                    ->whereNotNull('ends_at')
                    ->whereBetween('ends_at', [$segmentStart, $segmentEnd])
                    ->count();

                $activeTrend[] = $segmentActive;
                $expiredTrend[] = $segmentExpired;

                $segmentStart = $segmentEnd->copy()->addSecond();
            }

            $activeMembers = MembershipUser::whereIn('membership_id', $accessibleMemberships)
                ->where('ends_at', '>=', $segmentStart)
                ->where('created_at', '<=', $segmentEnd)
                ->count();

            $expiredMembers = array_sum($expiredTrend);

            // New members — first-time joiners
            $newMembers = DB::table('membership_users as mu1')
                ->select('mu1.user_id')
                ->whereIn('mu1.membership_id', $accessibleMemberships)
                ->whereBetween('mu1.created_at', [$start, $end])
                ->whereNotExists(function ($q) use ($start, $accessibleMemberships) {
                    $q->select(DB::raw(1))
                        ->from('membership_users as mu2')
                        ->whereColumn('mu1.user_id', 'mu2.user_id')
                        ->where('mu2.created_at', '<', $start)
                        ->whereIn('mu2.membership_id', $accessibleMemberships);
                })
                ->distinct()
                ->count();


            $totalUsers = $activeMembers + $expiredMembers;
            $churnRate = $totalUsers > 0 ? round(($expiredMembers / $totalUsers) * 100, 2) : 0;

            $renewals = max(0, $activeMembers - $newMembers);
            $renewalRate = $expiredMembers > 0 ? round(($renewals / $expiredMembers) * 100, 2) : 0;

            return response()->json([
                'total_users' => $totalUsers,
                'active_members' => $activeMembers,
                'expired_members' => $expiredMembers,
                'new_members' => $newMembers,
                'renewal_rate' => $renewalRate,
                'churn_rate' => $churnRate,
                'membership_trend' => [
                    'labels' => $labels,
                    'active' => $activeTrend,
                    'expired' => $expiredTrend,
                ]
            ]);

        } catch (\Throwable $e) {
            Log::error('Error in getBuildingMemberships: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while loading membership data.'], 500);
        }
    }

    public function getMaintenanceRequests(Request $request)
    {
        try {
            $start = $request->input('start')
                ? Carbon::parse($request->input('start'))->startOfDay()
                : now()->subDays(29)->startOfDay();

            $end = $request->input('end')
                ? Carbon::parse($request->input('end'))->endOfDay()
                : now()->endOfDay();

            $building_id = $request->input('building');
            $unit_id = $request->input('unit');

            $ownerService = new OwnerFiltersService();
            $accessibleBuildingIds = $ownerService->getAccessibleBuildingIds();

            if ($building_id && !in_array($building_id, $accessibleBuildingIds)) {
                return response()->json(['message' => 'You do not have access to the selected building.'], 403);
            }

            $buildingIds = $building_id ? [$building_id] : $accessibleBuildingIds;

            $totalDays = (int) $start->diffInDays($end) + 1;
            $maxSegments = 15;
            $daysPerSegment = max(1, ceil($totalDays / $maxSegments));

            $labels = [];
            $opened = [];
            $completed = [];
            $rejected = [];

            $segmentStart = $start->copy();
            while ($segmentStart->lte($end)) {
                $segmentEnd = $segmentStart->copy()->addDays($daysPerSegment - 1)->endOfDay();
                if ($segmentEnd->gt($end)) {
                    $segmentEnd = $end->copy()->endOfDay();
                }

                $label = $segmentStart->isSameDay($segmentEnd)
                    ? $segmentStart->format('d M')
                    : $segmentStart->format('d M') . ' - ' . $segmentEnd->format('d M');

                $labels[] = $label;

                $openedCount = Query::whereIn('building_id', $buildingIds)
                    ->when($unit_id, fn($q) => $q->where('unit_id', $unit_id))
                    ->whereBetween('created_at', [$segmentStart, $segmentEnd])
                    ->count();

                $completedCount = Query::whereIn('building_id', $buildingIds)
                    ->when($unit_id, fn($q) => $q->where('unit_id', $unit_id))
                    ->whereIn('status', ['Closed', 'Closed Late'])
                    ->whereBetween('created_at', [$segmentStart, $segmentEnd])
                    ->count();

                $rejectedCount = Query::whereIn('building_id', $buildingIds)
                    ->when($unit_id, fn($q) => $q->where('unit_id', $unit_id))
                    ->where('status', 'Rejected')
                    ->whereBetween('created_at', [$segmentStart, $segmentEnd])
                    ->count();

                $opened[] = $openedCount;
                $completed[] = $completedCount;
                $rejected[] = $rejectedCount;

                $segmentStart = $segmentEnd->copy()->addSecond();
            }

            return response()->json([
                'opened_requests' => array_sum($opened),
                'completed_requests' => array_sum($completed),
                'rejected_requests' => array_sum($rejected),
                'maintenance_trend' => [
                    'labels' => $labels,
                    'opened' => $opened,
                    'completed' => $completed,
                    'rejected' => $rejected,
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in Building Maintenance Chart: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while loading the maintenance data.'], 500);
        }
    }


    // Unit

    public function getUnitMaintenanceData(Request $request)
    {
        try {
            // Validate inputs (commented out for dummy data)
            /*
            $validated = $request->validate([
                'building' => 'required|exists:buildings,id',
                'unit' => 'required|exists:units,id',
                'start' => 'required|date',
                'end' => 'required|date|after_or_equal:start'
            ]);
            */

            $buildingId = $request->input('building');
            $unitId = $request->input('unit');
            $startDate = $request->input('start') ?? Carbon::now()->subMonth()->format('Y-m-d');
            $endDate = $request->input('end') ?? Carbon::now()->format('Y-m-d');

            // Generate dummy time labels (last 30 days)
            $timeLabels = [];
            $currentDate = Carbon::parse($startDate);
            $endDateObj = Carbon::parse($endDate);

            while ($currentDate <= $endDateObj) {
                $timeLabels[] = $currentDate->format('M d');
                $currentDate->addDay();
            }

            // Generate dummy chart data (random values between 0-5)
            $chartData = array_map(function() {
                return rand(0, 5);
            }, $timeLabels);

            // Generate dummy maintenance requests
            $statuses = ['opened', 'closed', 'rejected'];
            $departments = ['Electrical', 'Plumbing', 'HVAC', 'General'];

            $requests = [];
            for ($i = 0; $i < 15; $i++) {
                $randomDays = rand(0, 30);
                $randomStatus = $statuses[array_rand($statuses)];
                $randomDept = $departments[array_rand($departments)];

                $requests[] = [
                    'id' => $i + 1,
                    'department' => $randomDept,
                    'user' => 'User ' . ($i + 1),
                    'description' => 'Maintenance request for issue #' . ($i + 100),
                    'status' => $randomStatus,
                    'formatted_date' => Carbon::now()->subDays($randomDays)->format('Y-m-d H:i'),
                    'created_at' => Carbon::now()->subDays($randomDays)->toDateTimeString(),
                ];
            }

            // Calculate status counts
            $statusCounts = [
                'closed' => count(array_filter($requests, fn($r) => $r['status'] === 'closed')),
                'opened' => count(array_filter($requests, fn($r) => $r['status'] === 'opened')),
                'rejected' => count(array_filter($requests, fn($r) => $r['status'] === 'rejected')),
            ];

            return response()->json([
                'time_labels' => $timeLabels,
                'chart_data' => $chartData,
                'requests' => $requests,
                'status_counts' => $statusCounts,
                'meta' => [
                    'building_id' => $buildingId,
                    'unit_id' => $unitId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'generated_at' => now()->toDateTimeString(),
                    'note' => 'This is dummy data for testing purposes'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTrace() : null
            ], 500);
        }
    }

    // Get unit status history timeline
    public function getStatusHistory($unitId)
    {
        $data = [
            [
                'date' => 'June 1, 2023 - Present',
                'title' => 'Rented to John Smith',
                'description' => 'Monthly rent: $1,200 | Lease term: 12 months | Deposit: $1,200'
            ],
            [
                'date' => 'May 15 - May 31, 2023',
                'title' => 'Available for Rent',
                'description' => 'Listed at $1,250/month | 5 showings | 2 applications'
            ],
            [
                'date' => 'January 1 - May 14, 2023',
                'title' => 'Rented to Sarah Johnson',
                'description' => 'Monthly rent: $1,150 | Early termination due to relocation'
            ],
            [
                'date' => 'March 2022 - December 2022',
                'title' => 'Owned by Property Management',
                'description' => 'Used for corporate housing and short-term rentals'
            ],
            [
                'date' => 'February 15, 2022',
                'title' => 'Purchased by Property',
                'description' => 'Purchase price: $350,000 | Closing costs: $10,500'
            ]
        ];

        return response()->json($data);
    }

}
