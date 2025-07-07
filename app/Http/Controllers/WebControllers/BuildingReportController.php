<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BuildingUnit;
use App\Models\ManagerBuilding;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\UserBuildingUnit;
use App\Services\OwnerFiltersService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BuildingReportController extends Controller
{
    public function getMetrics(Request $request)
    {
        // Get filters from request
        $buildingId = $request->input('building_id', 'all');
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Dummy data - in a real app, you would query your database here
        $data = [
            'total_units' => $buildingId === 'all' ? 248 : 80,
            'total_levels' => $buildingId === 'all' ? 24 : 8,
            'total_income' => $buildingId === 'all' ? 24580 : 8500,
            'total_expenses' => $buildingId === 'all' ? 8420 : 3200,
        ];

        return response()->json($data);
    }

    public function getIncomeExpense(Request $request)
    {
        $building_id = $request->input('building');
        $start = $request->input('start');  // It should be 30 days back if not present
        $end = $request->input('end');      // It should be the right now if not present

        $types = ['Income', 'Expense'];
        $incomeTitles = ['Rent Payment', 'Sale Payment', 'Membership Payment'];
        $expenseTitles = ['Maintenance', 'Staff Salary'];
        $units = ['Apt 101', 'Apt 202', 'Apt 303', 'Common Area', 'Building'];

        $transactions = [];
        $count = 30;

        for ($i = 0; $i < $count; $i++) {
            $type = $types[array_rand($types)];
            $title = $type === 'Income'
                ? $incomeTitles[array_rand($incomeTitles)]
                : $expenseTitles[array_rand($expenseTitles)];

            $transactions[] = [
                'id' => 'TX-' . str_pad($i + 1000, 4, '0', STR_PAD_LEFT),
                'title' => $title,
                'unit' => $units[array_rand($units)],
                'type' => $type,
                'amount' => $type === 'Income' ? rand(50, 2000) : rand(100, 1500),
                'date' => Carbon::now()->subDays(rand(0, 30))->format('Y-m-d')
            ];
        }

        $data = [
            'total' => [
                'income' => 8500,
                'expense' => 1200,
            ],
            'growth' => [
                'income' => 20,
                'expense' => -10,
            ],
            'income_sources' => [
                'labels' => ['Rent', 'Sale', 'Memberships'],
                'data' => [6000, 1500, 700],
                'colors' => ['#184E83', '#1A6FC9', '#2ecc71']
            ],
            'expense_sources' => [
                'labels' => ['Maintenance'],
                'data' => [1200],
                'colors' => ['#ff4d6d']
            ],
            'recent_transactions' => $transactions
        ];

        return response()->json($data);
    }


    public function getOccupancy(Request $request)
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

            $totalDays = $start->diffInDays($end) + 1;
            $segments = min($totalDays, 15);
            $daysPerSegment = max(1, ceil($totalDays / $segments));

            $labels = [];
            $availableData = $rentedData = $soldData = [];

            $segmentStart = $start->copy();
            Log::info('Segment Start: ' . $segmentStart);
            while ($segmentStart->lte($end)) {
                $segmentEnd = $segmentStart->copy()->addDays($daysPerSegment - 1)->endOfDay();
                if ($segmentEnd->gt($end)) {
                    $segmentEnd = $end->copy()->endOfDay();
                }

                $label = $segmentStart->isSameDay($segmentEnd)
                    ? $segmentStart->format('d M')
                    : $segmentStart->format('d M') . ' - ' . $segmentEnd->format('d M');

                $labels[] = $label;

                $segmentUnits = $units->filter(fn($u) => $u->created_at <= $segmentEnd)->pluck('id')->toArray();

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

            $totalAvailable = max($availableData, 0);
            $totalRented = array_sum($rentedData);
            $totalSold = array_sum($soldData);
            $totalUnits = $totalAvailable + $totalRented + $totalSold;
            $occupiedUnits = $totalRented + $totalSold;

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


    // API 4: Staff data
    public function getStaff(Request $request)
    {
        $buildingId = $request->input('building_id', 'all');
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Dummy data
        $data = [
            'total_staff' => $buildingId === 'all' ? 48 : 20,
            'staff_by_department' => [
                'labels' => ['Maintenance', 'Security', 'Cleaning', 'Admin', 'Other'],
                'data' => $buildingId === 'all' ? [18, 12, 10, 5, 3] : [6, 4, 5, 3, 2],
                'colors' => ['#184E83', '#1A6FC9', '#2ecc71', '#ffbe0b', '#ff4d6d']
            ]
        ];

        return response()->json($data);
    }

    // API 5: Memberships data
    public function getMemberships(Request $request)
    {
        $buildingId = $request->input('building_id', 'all');
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Generate time labels based on date range
        $timeLabels = $this->generateTimeLabels($startDate, $endDate);

        // Dummy data
        $data = [
            'active_members' => $buildingId === 'all' ? 142 : 50,
            'expired_members' => $buildingId === 'all' ? 43 : 15,
            'new_members' => $buildingId === 'all' ? 24 : 8,
            'membership_trend' => [
                'labels' => $timeLabels,
                'active' => array_map(function() use ($buildingId) {
                    return $buildingId === 'all' ? rand(120, 142) : rand(40, 50);
                }, $timeLabels),
                'expired' => array_map(function() use ($buildingId) {
                    return $buildingId === 'all' ? rand(38, 43) : rand(14, 15);
                }, $timeLabels)
            ]
        ];

        return response()->json($data);
    }

    // API 6: Maintenance data
    public function getMaintenance(Request $request)
    {
        $buildingId = $request->input('building_id', 'all');
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Generate time labels based on date range
        $timeLabels = $this->generateTimeLabels($startDate, $endDate);

        // Dummy data with more realistic workflow patterns
        $completed = [];
        $pending = [];
        $rejected = [];

        // Start with some baseline numbers
        $baseCompleted = $buildingId === 'all' ? 10 : 4;
        $basePending = $buildingId === 'all' ? 5 : 2;
        $baseRejected = $buildingId === 'all' ? 2 : 1;

        // Generate trend data that shows workflow
        foreach ($timeLabels as $index => $label) {
            // Completed requests tend to follow pending requests from previous period
            $completed[] = $baseCompleted + ($index > 0 ? $pending[$index-1] * 0.8 : 0) + rand(0, 3);

            // New pending requests come in
            $pending[] = $basePending + rand(0, 2) + ($index % 3 === 0 ? 3 : 0); // spike every 3rd period

            // Rejections are a small percentage of pending
            $rejected[] = min($baseRejected + rand(0, 2), $pending[$index]); // Can't reject more than pending
        }

        $data = [
            'completed_requests' => array_sum($completed),
            'pending_requests' => array_sum($pending),
            'rejected_requests' => array_sum($rejected),
            'maintenance_trend' => [
                'labels' => $timeLabels,
                'completed' => $completed,
                'pending' => $pending,
                'rejected' => $rejected
            ]
        ];

        return response()->json($data);
    }

    // Helper function to generate time labels based on date range
    private function generateTimeLabels($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $diffDays = $end->diffInDays($start);

        if ($diffDays > 60) {
            // More than 2 months - show monthly
            $labels = [];
            $current = $start->copy();
            while ($current <= $end) {
                $labels[] = $current->format('M Y');
                $current->addMonth();
            }
            return $labels;
        } elseif ($diffDays > 14) {
            // 2 weeks to 2 months - show weekly
            $weeks = ceil($diffDays / 7);
            return array_map(function($i) { return "Week $i"; }, range(1, $weeks));
        } else {
            // Less than 2 weeks - show daily
            $labels = [];
            $current = $start->copy();
            while ($current <= $end) {
                $labels[] = $current->format('M d');
                $current->addDay();
            }
            return $labels;
        }
    }

}
