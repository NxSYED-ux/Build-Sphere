<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BuildingUnit;
use App\Models\Membership;
use App\Models\MembershipUsageLog;
use App\Models\MembershipUser;
use App\Models\StaffMember;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\UserBuildingUnit;
use App\Services\OwnerFiltersService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        try {
            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();
            $buildings = $ownerService->buildings($buildingIds);
            $units = $ownerService->allUnitsExceptMembershipUnits($buildingIds);
            $membershipsUnits = $ownerService->membershipsUnits($buildingIds);
            $memberships = $ownerService->memberships($buildingIds);

            return view('Heights.Owner.Dashboard.owner_dashboard', compact('buildings', 'units', 'membershipsUnits', 'memberships'));
        } catch (\Throwable $e) {
            Log::error('Error in Owner Dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function getStats()
    {
        try {
            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();

            $totalBuildings = count($buildingIds);
            $totalUnits = BuildingUnit::whereIn('building_id', $buildingIds)->count();
            $totalStaff = StaffMember::whereIn('building_id', $buildingIds)->count();

            return response()->json([
                'totalBuildings' => $totalBuildings,
                'totalUnits' => $totalUnits,
                'totalStaff' => $totalStaff,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch stats.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUnitOccupancy(Request $request)
    {
        try {
            $month = $request->input('month');
            $start = $month ? Carbon::parse($month)->startOfMonth() : now()->startOfMonth();
            $end = $month ? Carbon::parse($month)->endOfMonth() : now()->endOfMonth();

            $previousStart = (clone $start)->subMonth()->startOfMonth();
            $previousEnd = (clone $start)->subMonth()->endOfMonth();

            $building_id = $request->input('building');
            $ownerService = new OwnerFiltersService();
            $accessibleBuildingIds = $ownerService->getAccessibleBuildingIds();

            if ($building_id && !in_array($building_id, $accessibleBuildingIds)) {
                return response()->json([
                    'message' => 'You do not have access to the selected building.'
                ], 403);
            }

            $buildingIds = $building_id ? [$building_id] : $accessibleBuildingIds;

            $unitIdsTillCurrentEnd = BuildingUnit::whereIn('building_id', $buildingIds)
                ->where('sale_or_rent', '!=', 'Not Available')
                ->where('created_at', '<=', $end)
                ->pluck('id')
                ->toArray();

            $soldUnitTillCurrentPeriod = UserBuildingUnit::whereIn('unit_id', $unitIdsTillCurrentEnd)
                ->where('contract_status', 1)
                ->where('type', 'Sold')
                ->whereDate('created_at', '<=', $end)
                ->pluck('unit_id')
                ->unique()
                ->toArray();

            $rentedUnits = Subscription::where('source_name', 'unit contract')
                ->whereIn('unit_id', $unitIdsTillCurrentEnd)
                ->whereIn('building_id', $buildingIds)
                ->where(function ($q) use ($start) {
                    $q->whereNull('ends_at')
                        ->orWhereDate('ends_at', '>=', $start);
                })
                ->where('created_at', '<=', $end)
                ->pluck('unit_id')
                ->unique()
                ->toArray();

//            $soldUnits = UserBuildingUnit::whereIn('unit_id', $unitIdsTillCurrentEnd)
//                ->where('contract_status', 1)
//                ->where('type', 'Sold')
//                ->whereBetween('created_at', [$start, $end])
//                ->pluck('unit_id')
//                ->unique()
//                ->toArray();

            $currentOccupiedUnitIds = array_unique(array_merge($rentedUnits, $soldUnitTillCurrentPeriod));
            $available = count($unitIdsTillCurrentEnd) - count($currentOccupiedUnitIds);
            $rentedUnitsInCurrentPeriod = count($rentedUnits);
            $soldUnitsInCurrentPeriod = count($soldUnitTillCurrentPeriod);

            $unitIdsTillPreviousEnd = BuildingUnit::whereIn('building_id', $buildingIds)
                ->where('sale_or_rent', '!=', 'Not Available')
                ->where('created_at', '<=', $previousEnd)
                ->pluck('id')
                ->toArray();

            $soldUnitTillPreviousPeriod = UserBuildingUnit::whereIn('unit_id', $unitIdsTillPreviousEnd)
                ->where('contract_status', 1)
                ->where('type', 'Sold')
                ->whereDate('created_at', '<=', $previousEnd)
                ->pluck('unit_id')
                ->unique()
                ->toArray();

            $prevRentedUnits = Subscription::where('source_name', 'unit contract')
                ->whereIn('unit_id', $unitIdsTillPreviousEnd)
                ->whereIn('building_id', $buildingIds)
                ->where(function ($q) use ($previousStart) {
                    $q->whereNull('ends_at')
                        ->orWhereDate('ends_at', '>=', $previousStart);
                })
                ->where('created_at', '<=', $previousEnd)
                ->pluck('unit_id')
                ->unique()
                ->toArray();

//            $prevSoldUnits = UserBuildingUnit::whereIn('unit_id', $unitIdsTillPreviousEnd)
//                ->where('contract_status', 1)
//                ->where('type', 'Sold')
//                ->whereBetween('created_at', [$previousStart, $previousEnd])
//                ->pluck('unit_id')
//                ->unique()
//                ->toArray();

            $prevOccupiedUnitIds = array_unique(array_merge($prevRentedUnits, $soldUnitTillPreviousPeriod));
            $prevAvailable = count($unitIdsTillPreviousEnd) - count($prevOccupiedUnitIds);
            $prevRentedCount = count($prevRentedUnits);
            $prevSoldCount = count($soldUnitTillPreviousPeriod);

            $rentedGrowth = $this->calculateGrowth($rentedUnitsInCurrentPeriod, $prevRentedCount);
            $soldGrowth = $this->calculateGrowth($soldUnitsInCurrentPeriod, $prevSoldCount);
            $availableGrowth = $this->calculateGrowth($available, $prevAvailable);

            return response()->json([
                'available' => max($available, 0),
                'rented' => $rentedUnitsInCurrentPeriod,
                'sold' => $soldUnitsInCurrentPeriod,
                'growth' => [
                    'available' => $availableGrowth,
                    'rented' => $rentedGrowth,
                    'sold' => $soldGrowth,
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in Owner Dashboard (Unit Occupancy): ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while calculating occupancy.'
            ], 500);
        }
    }

    public function getMembershipSubscriptionStats(Request $request)
    {
        try {
            $building_id = $request->input('building');
            $membership_id = $request->input('membership');
            $month = $request->input('month');

            $ownerService = new OwnerFiltersService();
            $accessibleBuildingIds = $ownerService->getAccessibleBuildingIds();

            if ($building_id && !in_array($building_id, $accessibleBuildingIds)) {
                return response()->json([
                    'message' => 'You do not have access to the selected building.'
                ], 403);
            }

            $buildingIds = $building_id ? [$building_id] : $accessibleBuildingIds;
            $validMemberships = $ownerService->memberships($buildingIds);
            $accessibleMemberships = $validMemberships->pluck('id')->toArray();

            if ($membership_id && !in_array($membership_id, $accessibleMemberships)) {
                return response()->json([
                    'message' => 'You do not have access to the selected membership record.'
                ], 403);
            }

            $membershipIds = $membership_id ? [$membership_id] : $accessibleMemberships;

            $start = $month ? Carbon::parse($month)->startOfMonth() : now()->startOfMonth();
            $end = $month ? Carbon::parse($month)->endOfMonth() : now()->endOfMonth();

            $previousStart = (clone $start)->subMonth()->startOfMonth();
            $previousEnd = (clone $start)->subMonth()->endOfMonth();

            $baseQuery = MembershipUser::whereIn('membership_id', $membershipIds);

            $activeCurrent = (clone $baseQuery)
                ->where('status', 1)
                ->where('created_at', '<=', $end)
                ->where('ends_at', '>=', $start)
                ->count();


            $expiredCurrent = (clone $baseQuery)
                ->where('status', 0)
                ->whereBetween('ends_at', [$start, $end])
                ->count();

            $activePrevious = (clone $baseQuery)
                ->where('status', 1)
                ->where('created_at', '<=', $previousEnd)
                ->where('ends_at', '>=', $previousStart)
                ->count();

            $expiredPrevious = (clone $baseQuery)
                ->where('status', 0)
                ->whereBetween('ends_at', [$previousStart, $previousEnd])
                ->count();


            // Current Month Usage
            $membershipUserIds = (clone $baseQuery)->pluck('id');
            $totalUsed = MembershipUsageLog::whereIn('membership_user_id', $membershipUserIds)
                ->whereBetween('usage_date', [$start, $end])
                ->sum('used');

            $today = now();
            if ($end->lessThan($today)) {
                $monthEndForCalc = $end;
            } elseif ($start->greaterThan($today)) {
                $monthEndForCalc = null;
            } else {
                $monthEndForCalc = $today;
            }

            $daysPassed = $monthEndForCalc ? $start->diffInDays($monthEndForCalc) + 1 : 0;
            $totalQuantity = (clone $baseQuery)
                ->where('ends_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->sum('quantity');

            $allowedUsage = $totalQuantity * $daysPassed;
            $usagePercent = $allowedUsage > 0 ? round(($totalUsed / $allowedUsage) * 100, 1) : 0;

            $getGrowth = function ($current, $previous) {
                if ($previous == 0 && $current == 0) return 0;
                if ($previous == 0) return 100;
                return round((($current - $previous) / $previous) * 100, 1);
            };

            return response()->json([
                'active' => $activeCurrent,
                'expired' => $expiredCurrent,
                'usage' => $usagePercent,
                'growth' => [
                    'active' => $getGrowth($activeCurrent, $activePrevious),
                    'expired' => $getGrowth($expiredCurrent, $expiredPrevious),
                ]
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUnitStatus(Request $request)
    {
        try {
            $year = $request->input('year', now()->year);

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
                ->whereYear('created_at', '<=', $year)
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

            $labels = collect(range(1, 12))->map(fn($m) => Carbon::create()->month($m)->format('F'))->toArray();
            $availableData = $rentedData = $soldData = array_fill(0, 12, 0);

            foreach (range(1, 12) as $month) {
                $monthStart = Carbon::create($year, $month)->startOfMonth();
                $monthEnd = Carbon::create($year, $month)->endOfMonth();

                $monthlyUnits = $units->filter(fn($unit) => $unit->created_at <= $monthEnd)->pluck('id')->toArray();

                $monthlySold = $soldContracts
                    ->filter(fn($s) => in_array($s->unit_id, $monthlyUnits) && $s->created_at <= $monthEnd)
                    ->pluck('unit_id')
                    ->unique()
                    ->toArray();

                $soldCount = $soldContracts
                    ->filter(fn($s) => in_array($s->unit_id, $monthlyUnits) && $s->created_at->between($monthStart, $monthEnd))
                    ->pluck('unit_id')
                    ->unique()
                    ->count();

                $monthlyRented = $subscriptions
                    ->filter(fn($s) => in_array($s->unit_id, $monthlyUnits) && (is_null($s->ends_at) || $s->ends_at >= $monthStart) && ($s->created_at <= $monthEnd))
                    ->pluck('unit_id')
                    ->unique()
                    ->toArray();

                $rentedCount = $subscriptions
                    ->filter(fn($s) => in_array($s->unit_id, $monthlyUnits) && $s->created_at->between($monthStart, $monthEnd))
                    ->pluck('unit_id')
                    ->unique()
                    ->count();

                $occupied = array_unique(array_merge($monthlyRented, $monthlySold));
                $available = count($monthlyUnits) - count($occupied);

                $availableData[$month - 1] = max($available, 0);
                $rentedData[$month - 1] = $rentedCount;
                $soldData[$month - 1] = $soldCount;
            }

            return response()->json([
                'labels' => $labels,
                'available' => $availableData,
                'rented' => $rentedData,
                'sold' => $soldData,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in Owner Dashboard (Monthly Chart): ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while preparing the monthly occupancy chart.'
            ], 500);
        }
    }

    public function getStaffDistribution(Request $request)
    {
        try {
            $buildingId = $request->input('building');

            $ownerService = new OwnerFiltersService();
            $accessibleBuildingIds = $ownerService->getAccessibleBuildingIds();
            $departments = $ownerService->departments();
            $departmentsMap = $departments->pluck('name', 'id')->toArray();

            $buildingIds = $buildingId ? [$buildingId] : $accessibleBuildingIds;

            $start = Carbon::parse($request->input('start', now()->subDays(29)))->startOfDay();
            $end = Carbon::parse($request->input('end', now()))->endOfDay();

            $staffGrouped = StaffMember::query()
                ->whereNotNull('department_id')
                ->whereBetween('joined_at', [$start, $end])
                ->whereIn('building_id', $buildingIds)
                ->select('department_id', DB::raw('COUNT(*) as total'))
                ->groupBy('department_id')
                ->get();

            $whereIds = $buildingId ? [$buildingId] : $accessibleBuildingIds;

            $managerCount = StaffMember::with('managerBuildings')
                ->whereNull('department_id')
                ->whereBetween('joined_at', [$start, $end])
                ->whereHas('managerBuildings', function ($q) use ($whereIds) {
                    $q->whereIn('building_id', $whereIds);
                })->count();


            $labels = [];
            $dataMap = [];

            foreach ($departmentsMap as $id => $name) {
                $labels[] = $name;
                $dataMap[$id] = 0;
            }

            foreach ($staffGrouped as $item) {
                $dataMap[$item->department_id] = $item->total;
            }

            $data = [];
            foreach ($departmentsMap as $id => $name) {
                $data[] = $dataMap[$id];
            }

            $labels[] = 'Manager';
            $data[] = $managerCount;

            return response()->json([
                'labels' => $labels,
                'data' => $data,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error in Owner Dashboard (getStaffDistribution): ' . $e->getMessage());
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getIncomeExpense(Request $request)
    {
        try {
            $year = $request->input('year', now()->year);
            $building_id = $request->input('building');
            $source = strtolower($request->input('source'));
            $source_id = $request->input('source_id');
            $organization_id = 1;

            $roleId = request()->user()->role_id;

            if ($roleId !== 2) {
                $ownerService = new OwnerFiltersService();
                $accessibleBuildingIds = $ownerService->getAccessibleBuildingIds();

                if ($building_id && !in_array($building_id, $accessibleBuildingIds)) {
                    return response()->json([
                        'message' => 'You do not have access to the selected building.'
                    ], 403);
                }
            }

            $query = Transaction::query()->whereYear('created_at', $year);

            $incomeQuery = clone $query;
            $incomeQuery->where('seller_type', 'organization')
                ->where('seller_id', $organization_id)
                ->when($roleId !== 2, fn($q) => $q->whereIn('building_id', $accessibleBuildingIds))
                ->when($building_id, fn($q) => $q->where('building_id', $building_id));

            $expenseQuery = clone $query;
            $expenseQuery->where('buyer_type', 'organization')
                ->where('buyer_id', $organization_id)
                ->when($roleId !== 2, fn($q) => $q->whereIn('building_id', $accessibleBuildingIds))
                ->when($building_id, fn($q) => $q->where('building_id', $building_id));

            switch ($source) {
                case 'membership':
                    if ($source_id) {
                        $incomeQuery->where('membership_id', $source_id);
                        $expenseQuery->where('membership_id', $source_id);
                    } else {
                        $incomeQuery->whereNotNull('membership_id');
                        $expenseQuery->whereNotNull('membership_id');
                    }
                    break;

                case 'request':
                    $incomeQuery->where('source_name', 'query');
                    $expenseQuery->where('source_name', 'query');

                    if ($source_id) {
                        $incomeQuery->where('unit_id', $source_id);
                        $expenseQuery->where('unit_id', $source_id);
                    } else {
                        $incomeQuery->whereNotNull('unit_id');
                        $expenseQuery->whereNotNull('unit_id');
                    }
                    break;

                case 'sale':
                    $incomeQuery->where('source_name', 'unit contract');
                    $expenseQuery->where('source_name', 'unit contract');

                    if ($source_id) {
                        $incomeQuery->where('unit_id', $source_id);
                        $expenseQuery->where('unit_id', $source_id);
                    } else {
                        $incomeQuery->whereNotNull('unit_id');
                        $expenseQuery->whereNotNull('unit_id');
                    }
                    break;

                case 'rent':
                    $incomeQuery->where('source_name', 'subscription')
                        ->whereExists(function ($query) use ($source_id) {
                            $query->select(DB::raw(1))
                                ->from('subscriptions')
                                ->whereColumn('subscriptions.id', 'transactions.source_id')
                                ->where('subscriptions.source_name', 'unit contract')
                                ->when($source_id, function ($q) use ($source_id) {
                                    $q->where('subscriptions.unit_id', $source_id);
                                });
                        });

                    $expenseQuery->where('source_name', 'subscription')
                        ->whereExists(function ($query) use ($source_id) {
                            $query->select(DB::raw(1))
                                ->from('subscriptions')
                                ->whereColumn('subscriptions.id', 'transactions.source_id')
                                ->where('subscriptions.source_name', 'unit contract')
                                ->when($source_id, function ($q) use ($source_id) {
                                    $q->where('subscriptions.unit_id', $source_id);
                                });
                        });
                    break;

                case 'facility':
                    $facilityFilter = function ($q) use ($source_id) {
                        if ($source_id) {
                            $q->where('unit_id', $source_id);
                        } else {
                            $q->whereNotNull('unit_id');
                        }

                        $q->whereNotNull('membership_id')
                            ->where(function ($q2) {
                                $q2->where('source_name', 'membership')
                                    ->orWhere('source_name', 'subscription');
                            });
                    };

                    $incomeQuery->where($facilityFilter);
                    $expenseQuery->where($facilityFilter);
                    break;

                case 'charges':
                    $incomeQuery->whereRaw('1 = 0'); // Force no income results
                    $expenseQuery->where('seller_type', 'platform');
                    break;
            }

            $incomeData = $incomeQuery->selectRaw('MONTH(created_at) as month, SUM(price) as total')
                ->groupBy('month')
                ->pluck('total', 'month');

            $expenseData = $expenseQuery->selectRaw('MONTH(created_at) as month, SUM(price) as total')
                ->groupBy('month')
                ->pluck('total', 'month');

            $income = [];
            $expenses = [];
            for ($i = 1; $i <= 12; $i++) {
                $income[] = round($incomeData[$i] ?? 0, 2);
                $expenses[] = round($expenseData[$i] ?? 0, 2);
            }

            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            return response()->json([
                'labels' => $labels,
                'income' => $income,
                'expenses' => $expenses
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in getIncomeExpense: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve income and expense data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMembershipDistribution(Request $request)
    {
        try {
            $month = $request->input('month');
            $building_id = $request->input('building');

            $ownerService = new OwnerFiltersService();
            $accessibleBuildingIds = $ownerService->getAccessibleBuildingIds();

            if ($building_id && !in_array($building_id, $accessibleBuildingIds)) {
                return response()->json([
                    'message' => 'You do not have access to the selected building.'
                ], 403);
            }

            $buildingIds = $building_id ? [$building_id] : $accessibleBuildingIds;

            $validMemberships = $ownerService->memberships($buildingIds);
            $accessibleMemberships = $validMemberships->pluck('id')->toArray();

            $start = $month ? Carbon::parse($month)->startOfMonth() : now()->startOfMonth();
            $end = $month ? Carbon::parse($month)->endOfMonth() : now()->endOfMonth();

            $grouped = MembershipUser::query()
                ->whereBetween('created_at', [$start, $end])
                ->whereIn('membership_id', $accessibleMemberships)
                ->select('membership_id', DB::raw('COUNT(*) as total'))
                ->groupBy('membership_id')
                ->get();

            $membershipNames = Membership::whereIn('id', $grouped->pluck('membership_id'))->pluck('name', 'id');

            $labels = [];
            $values = [];

            foreach ($grouped as $item) {
                $labels[] = $membershipNames[$item->membership_id] ?? 'Unknown';
                $values[] = $item->total;
            }

            return response()->json([
                'labels' => $labels,
                'values' => $values,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error in getMembershipDistribution: ' . $e->getMessage());
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // Helper Function
    private function calculateGrowth(int $current, int $previous): float
    {
        if ($previous > 0) {
            return round((($current - $previous) / $previous) * 100, 2);
        }

        return $current > 0 ? 100.0 : 0.0;
    }

//    public function getUnitStatus(Request $request)
//    {
//        try {
//            $start = $request->input('start') ? Carbon::parse($request->input('start'))->startOfDay() : now()->subDays(30)->startOfDay();
//            $end = $request->input('end') ? Carbon::parse($request->input('end'))->endOfDay() : now()->endOfDay();
//
//            $building_id = $request->input('building');
//            $ownerService = new OwnerFiltersService();
//            $accessibleBuildingIds = $ownerService->getAccessibleBuildingIds();
//
//            if ($building_id && !in_array($building_id, $accessibleBuildingIds)) {
//                return response()->json([
//                    'message' => 'You do not have access to the selected building.'
//                ], 403);
//            }
//
//            $buildingIds = $building_id ? [$building_id] : $accessibleBuildingIds;
//
//            $units = BuildingUnit::whereIn('building_id', $buildingIds)
//                ->where('sale_or_rent', '!=', 'Not Available')
//                ->where('created_at', '<=', $end)
//                ->get(['id', 'created_at']);
//
//            $unitIds = $units->pluck('id')->toArray();
//
//            $soldContracts = UserBuildingUnit::whereIn('unit_id', $unitIds)
//                ->where('contract_status', 1)
//                ->where('type', 'Sold')
//                ->get(['unit_id', 'created_at']);
//
//            $subscriptions = Subscription::where('source_name', 'unit contract')
//                ->whereIn('unit_id', $unitIds)
//                ->whereIn('building_id', $buildingIds)
//                ->get(['unit_id', 'created_at', 'ends_at']);
//
//            $totalDays = $start->diffInDays($end) + 1;
//            $segments = min($totalDays, 15);
//            $daysPerSegment = max(1, ceil($totalDays / $segments));
//
//            $labels = [];
//            $availableData = $rentedData = $soldData = [];
//
//            $segmentStart = $start->copy();
//            while ($segmentStart->lte($end)) {
//                $segmentEnd = $segmentStart->copy()->addDays($daysPerSegment - 1)->endOfDay();
//                if ($segmentEnd->gt($end)) {
//                    $segmentEnd = $end->copy()->endOfDay();
//                }
//
//                $label = $segmentStart->isSameDay($segmentEnd)
//                    ? $segmentStart->format('d M')
//                    : $segmentStart->format('d M') . ' - ' . $segmentEnd->format('d M');
//
//                $labels[] = $label;
//
//                $segmentUnits = $units->filter(fn($u) => $u->created_at <= $segmentEnd)->pluck('id')->toArray();
//
//                $segmentSold = $soldContracts
//                    ->filter(fn($s) => in_array($s->unit_id, $segmentUnits) && $s->created_at <= $segmentEnd)
//                    ->pluck('unit_id')
//                    ->unique()
//                    ->toArray();
//
//                $soldCount = $soldContracts
//                    ->filter(fn($s) => in_array($s->unit_id, $segmentUnits) && $s->created_at->between($segmentStart, $segmentEnd))
//                    ->pluck('unit_id')
//                    ->unique()
//                    ->count();
//
//                $segmentRented = $subscriptions
//                    ->filter(fn($s) => in_array($s->unit_id, $segmentUnits) && (is_null($s->ends_at) || $s->ends_at >= $segmentStart) && ($s->created_at <= $segmentEnd))
//                    ->pluck('unit_id')
//                    ->unique()
//                    ->toArray();
//
//                $rentedCount = $subscriptions
//                    ->filter(fn($s) => in_array($s->unit_id, $segmentUnits) && $s->created_at->between($segmentStart, $segmentEnd))
//                    ->pluck('unit_id')
//                    ->unique()
//                    ->count();
//
//                $occupied = array_unique(array_merge($segmentRented, $segmentSold));
//                $available = count($segmentUnits) - count($occupied);
//
//                $availableData[] = max($available, 0);
//                $rentedData[] = $rentedCount;
//                $soldData[] = $soldCount;
//
//                $segmentStart = $segmentEnd->copy()->addSecond();
//            }
//
//            return response()->json([
//                'labels' => $labels,
//                'available' => $availableData,
//                'rented' => $rentedData,
//                'sold' => $soldData,
//            ]);
//        } catch (\Throwable $e) {
//            Log::error('Error in Owner Dashboard (Segmented Status): ' . $e->getMessage());
//            return response()->json([
//                'message' => 'An error occurred while preparing the unit occupancy chart.'
//            ], 500);
//        }
//    }

}
