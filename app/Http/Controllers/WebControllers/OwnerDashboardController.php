<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BuildingUnit;
use App\Models\StaffMember;
use App\Models\Subscription;
use App\Models\UserBuildingUnit;
use App\Services\OwnerFiltersService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        try {
            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();
            $buildings = $ownerService->buildings($buildingIds);
            $units = $ownerService->units($buildingIds);
            $memberships = $ownerService->memberships($buildingIds);

            return view('Heights.Owner.Dashboard.owner_dashboard', compact('buildings', 'units', 'memberships'));
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


    // Get membership plans data
    public function getMembershipPlans(Request $request)
    {
        $range = $request->input('range', '30days');
        $planType = $request->input('plan_type', 'all');

        // Base data
        $data = [
            'active' => 65,
            'expired' => 12,
            'usage' => 75,
            'trend' => [
                'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                'values' => [10, 15, 18, 22]
            ]
        ];

        // Apply plan type filter simulation
        if ($planType !== 'all') {
            $multiplier = match($planType) {
                'basic' => 0.5,
                'premium' => 0.3,
                'enterprise' => 0.2,
                default => 1
            };

            $data['active'] = round($data['active'] * $multiplier);
            $data['expired'] = round($data['expired'] * $multiplier);
        }

        // Apply date range simulation
        if ($range === '7days') {
            $data['trend']['values'] = array_slice($data['trend']['values'], -2);
            $data['trend']['labels'] = array_slice($data['trend']['labels'], -2);
        } elseif ($range === '90days') {
            $data['active'] = round($data['active'] * 1.3);
            $data['expired'] = round($data['expired'] * 1.3);
        }

        return response()->json($data);
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
        $range = $request->input('range', 'all');
        $buildingId = $request->input('building', 'all');

        $managers = 8;
        $staff = 22;

        // Apply building filter simulation
        if ($buildingId !== 'all') {
            $multiplier = match($buildingId) {
                'building1' => 0.4,
                'building2' => 0.3,
                'building3' => 0.3,
                default => 1
            };

            $managers = round($managers * $multiplier);
            $staff = round($staff * $multiplier);
        }

        // Apply date range simulation
        if ($range === '7days') {
            $managers = round($managers * 0.2);
            $staff = round($staff * 0.2);
        } elseif ($range === '90days') {
            $managers = round($managers * 1.3);
            $staff = round($staff * 1.3);
        }

        return response()->json([
            'managers' => $managers,
            'staff' => $staff
        ]);
    }


    public function getIncomeExpense(Request $request)
    {
        $range = $request->input('range', '12months');
        $buildingId = $request->input('building', 'all');

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $income = [8500, 12000, 9500, 11000, 13500, 16000, 14000, 15500, 14500, 17000, 18500, 20000];
        $expenses = [5000, 7500, 6000, 7000, 9000, 11000, 9500, 10000, 9000, 11500, 12500, 13500];

        // Apply building filter simulation
        if ($buildingId !== 'all') {
            $multiplier = match($buildingId) {
                'building1' => 0.4,
                'building2' => 0.3,
                'building3' => 0.3,
                default => 1
            };

            $income = array_map(fn($val) => round($val * $multiplier), $income);
            $expenses = array_map(fn($val) => round($val * $multiplier), $expenses);
        }

        // Apply date range
        if ($range === '3months') {
            $labels = array_slice($labels, -3);
            $income = array_slice($income, -3);
            $expenses = array_slice($expenses, -3);
        } elseif ($range === '6months') {
            $labels = array_slice($labels, -6);
            $income = array_slice($income, -6);
            $expenses = array_slice($expenses, -6);
        }

        return response()->json([
            'labels' => $labels,
            'income' => $income,
            'expenses' => $expenses
        ]);
    }


    public function getMembershipPlanUsage(Request $request)
    {
        $range = $request->input('range', '30days');
        $status = $request->input('status', 'all');

        $labels = ['Basic', 'Premium', 'Enterprise', 'Custom'];
        $values = [45, 30, 15, 10];

        // Apply status filter simulation
        if ($status !== 'all') {
            $multiplier = match($status) {
                'active' => [0.8, 0.7, 0.6, 0.5],
                'expired' => [0.2, 0.3, 0.4, 0.5],
                default => [1, 1, 1, 1]
            };

            $values = array_map(fn($val, $mult) => round($val * $mult), $values, $multiplier);
        }

        // Apply date range simulation
        if ($range === '7days') {
            $values = array_map(fn($val) => round($val * 0.2), $values);
        } elseif ($range === '90days') {
            $values = array_map(fn($val) => round($val * 1.3), $values);
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }


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
