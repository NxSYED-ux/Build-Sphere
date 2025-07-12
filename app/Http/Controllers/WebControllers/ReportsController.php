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

            return view('Heights.Owner.Reports.index', compact('buildings'));
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

            if($unit_id && !$building_id){
                return response()->json(['message' => 'Building is required'], 401);
            }

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

            if($unit_id && !$building_id){
                return response()->json(['message' => 'Building is required'], 401);
            }

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
            $start = $request->input('start')
                ? Carbon::parse($request->input('start'))->startOfDay()
                : now()->subDays(29)->startOfDay();

            $end = $request->input('end')
                ? Carbon::parse($request->input('end'))->endOfDay()
                : now()->endOfDay();

            $building_id = $request->input('building');
            $unit_id = $request->input('unit');

            if ($unit_id && !$building_id) {
                return response()->json(['message' => 'Building is required'], 401);
            }

            $ownerService = new OwnerFiltersService();
            $accessibleBuildingIds = $ownerService->getAccessibleBuildingIds();

            if ($building_id && !in_array($building_id, $accessibleBuildingIds)) {
                return response()->json(['message' => 'You do not have access to the selected building.'], 403);
            }

            $buildingIds = $building_id ? [$building_id] : $accessibleBuildingIds;

            $queries = Query::with(['department:id,name', 'user:id,name', 'staffMember:id,user_id', 'staffMember.user:id,name'])
                ->whereIn('building_id', $buildingIds)
                ->when($unit_id, fn($q) => $q->where('unit_id', $unit_id))
                ->whereBetween('created_at', [$start, $end])
                ->get();

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

                $segmentData = $queries->filter(function ($query) use ($segmentStart, $segmentEnd) {
                    return $query->created_at >= $segmentStart && $query->created_at <= $segmentEnd;
                });

                $openedCount = $segmentData->count();
                $completedCount = $segmentData->whereIn('status', ['Closed', 'Closed Late'])->count();
                $rejectedCount = $segmentData->where('status', 'Rejected')->count();

                $labels[] = $label;
                $opened[] = $openedCount;
                $completed[] = $completedCount;
                $rejected[] = $rejectedCount;

                $segmentStart = $segmentEnd->copy()->addSecond();
            }

            $requestsData = collect($queries)
                ->sortByDesc('created_at')
                ->map(function ($query) {
                    return [
                        'id' => 'QR-' . str_pad($query->id, 4, '0', STR_PAD_LEFT),
                        'department' => $query->department?->name ?? 'N/A',
                        'description' => $query->description,
                        'staff' => $query->staffMember?->user?->name ?? 'N/A',
                        'user' => $query->user?->name ?? 'N/A',
                        'expense' => $query->expense,
                        'date' => $query->created_at->format('Y-m-d'),
                        'status' => $query->status,
                    ];
                })
                ->values()
                ->toArray();

            return response()->json([
                'opened_requests' => array_sum($opened),
                'completed_requests' => array_sum($completed),
                'rejected_requests' => array_sum($rejected),
                'maintenance_trend' => [
                    'labels' => $labels,
                    'opened' => $opened,
                    'completed' => $completed,
                    'rejected' => $rejected,
                ],
                'maintenanceData' => $requestsData,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error in Unit Maintenance Chart: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while loading the maintenance data.'], 500);
        }
    }

    public function getPeriodContracts(Request $request)
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

            if ($unit_id && !$building_id) {
                return response()->json(['message' => 'Building is required'], 401);
            }

            $ownerService = new OwnerFiltersService();
            $accessibleBuildingIds = $ownerService->getAccessibleBuildingIds();

            if ($building_id && !in_array($building_id, $accessibleBuildingIds)) {
                return response()->json(['message' => 'You do not have access to the selected building.'], 403);
            }

            $buildingIds = $building_id ? [$building_id] : $accessibleBuildingIds;

            $contracts = UserBuildingUnit::with(['user:id,name', 'subscription:id,created_at,ends_at,price_at_subscription'])
                ->whereIn('building_id', $buildingIds)
                ->when($unit_id, fn($q) => $q->where('unit_id', $unit_id))
                ->whereBetween('created_at', [$start, $end])
                ->orderByDesc('created_at')
                ->get();

            $lastContract = $contracts->first();

            if ($lastContract) {
                $lastContract->load(['user:id,name,email,cnic,phone_no', 'subscription:id,created_at,ends_at,price_at_subscription']);
            }

            $contractsWithinPeriod = $contracts->map(function ($details) {
                return [
                    'date' => $details->type === 'Rented'
                        ? $details->subscription?->created_at->format('Y-m-d') . ' - ' . optional($details->subscription?->ends_at)->format('Y-m-d')
                        : $details->created_at->format('Y-m-d'),
                    'tittle' => $details->type . ' to ' . ($details->user?->name ?? 'N/A'),
                    'price' => 'PKR ' . ($details->subscription?->price_at_subscription ?? $details->price),
                ];
            })->values()->toArray();

            return response()->json([
                'lastContract' => $lastContract,
                'contracts' => $contractsWithinPeriod,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error in Unit Maintenance Chart: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while loading the maintenance data.'], 500);
        }
    }

}
