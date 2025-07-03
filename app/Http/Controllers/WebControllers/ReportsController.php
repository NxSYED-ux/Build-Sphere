<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BuildingUnit;
use App\Models\ManagerBuilding;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{
    public function getOccupancyStats(Request $request){
        $user = $request->user();
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];
        $role_id = $token['role_id'];

        return $this->orgOccupancyStats($request, $organization_id, $role_id, $user->id, 'user_id');
    }

    public function getOrgMonthlyFinancialStats(Request $request)
    {
        $user = $request->user();
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];
        $role_id = $token['role_id'];

        return $this->orgMonthlyStats($request, $organization_id, $role_id, $user->id, 'user_id');
    }


    // Manager Detail Page
    public function getManagerBuildingsMonthlyStats(Request $request, string $id)
    {
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        return $this->orgMonthlyStats($request, $organization_id, 3, $id, 'staff_id');
    }

    public function getManagerBuildingsOccupancyStats(Request $request, string $id)
    {
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        return $this->orgOccupancyStats($request, $organization_id, 3, $id, 'staff_id');
    }


    // Helper function
    private function orgOccupancyStats(Request $request, string $organization_id, int $roleId, string $id, string $trackOn)
    {
        $building_id = $request->input('buildingId');

        try {
            $buildingIds = [];
            if ($roleId === 3) {
                $buildingIds = ManagerBuilding::where($trackOn, $id)->pluck('building_id')->toArray();

                if ($building_id && !in_array($building_id, $buildingIds)) {
                    return response()->json(['error' => 'You do not have access to this building.'], 403);
                }
            }

            $units = BuildingUnit::where('organization_id', $organization_id)
                ->when($building_id, function ($query) use ($building_id) {
                    $query->where('building_id', $building_id);
                }, function ($query) use ($roleId, $buildingIds) {
                    if ($roleId === 3) {
                        $query->whereIn('building_id', $buildingIds);
                    }
                })
                ->select('availability_status')
                ->get()
                ->groupBy('availability_status')
                ->map->count();

            return response()->json([
                'availableUnits' => $units['Available'] ?? 0,
                'rentedUnits' => $units['Rented'] ?? 0,
                'soldUnits' => $units['Sold'] ?? 0,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error in occupancy chart: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }

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


    public function getMetrics(Request $request)
    {
        $params = $this->getFilterParams($request);

        // Simulated data - replace with actual database queries
        $data = [
            'total_units' => $params['building_id'] == 'all' ? 248 : 80,
            'total_levels' => $params['building_id'] == 'all' ? 24 : 8,
            'total_income' => $params['building_id'] == 'all' ? 24580 : 8500,
            'total_expenses' => $params['building_id'] == 'all' ? 8420 : 3200,
            'units_change' => $params['building_id'] == 'all' ? 5 : 2,
            'levels_change' => 0,
            'income_change' => $params['building_id'] == 'all' ? 12 : 8,
            'expenses_change' => $params['building_id'] == 'all' ? -5 : -3,
        ];

        return response()->json($data);
    }

    public function getIncomeExpense(Request $request)
    {
        $params = $this->getFilterParams($request);

        $data = [
            'income' => $params['building_id'] == 'all' ? 24580 : 8500,
            'expenses' => $params['building_id'] == 'all' ? 8420 : 3200,
            'income_growth' => $params['building_id'] == 'all' ? 12 : 8,
            'expense_growth' => $params['building_id'] == 'all' ? -5 : -3,
        ];

        return response()->json($data);
    }

    public function getIncomeSources(Request $request)
    {
        $params = $this->getFilterParams($request);

        $data = [
            'labels' => ['Rent', 'Parking', 'Amenities', 'Other'],
            'data' => $params['building_id'] == 'all' ? [18000, 4200, 1500, 880] : [6000, 1500, 700, 300],
            'colors' => ['#184E83', '#1A6FC9', '#2ecc71', '#ffbe0b']
        ];

        return response()->json($data);
    }

    public function getExpenseCategories(Request $request)
    {
        $params = $this->getFilterParams($request);

        $data = [
            'labels' => ['Maintenance', 'Utilities', 'Staff', 'Insurance', 'Other'],
            'data' => $params['building_id'] == 'all' ? [3200, 2800, 1500, 500, 420] : [1200, 1000, 600, 200, 200],
            'colors' => ['#ff4d6d', '#ff758f', '#ff8fa3', '#ffb3c1', '#ffccd5']
        ];

        return response()->json($data);
    }

    public function getOccupancy(Request $request)
    {
        $params = $this->getFilterParams($request);

        // Generate time-based data based on the selected range
        $timeLabels = $this->getTimeLabels($params['range'], $params['start_date'] ?? null, $params['end_date'] ?? null);

        $availableData = array_map(function() { return rand(5, 15); }, $timeLabels);
        $rentedData = array_map(function() { return rand(150, 200); }, $timeLabels);
        $soldData = array_map(function() { return rand(5, 15); }, $timeLabels);

        $data = [
            'labels' => $timeLabels,
            'available' => $availableData,
            'rented' => $rentedData,
            'sold' => $soldData,
            'occupancy_rate' => $params['building_id'] == 'all' ? 92 : 90,
            'rented_units' => $params['building_id'] == 'all' ? 200 : 70,
            'sold_units' => $params['building_id'] == 'all' ? 28 : 4,
            'available_units' => $params['building_id'] == 'all' ? 20 : 6,
        ];

        return response()->json($data);
    }

    public function getStaff(Request $request)
    {
        $params = $this->getFilterParams($request);

        $data = [
            'labels' => ['Maintenance', 'Security', 'Cleaning', 'Admin', 'Other'],
            'data' => $params['building_id'] == 'all' ? [18, 12, 10, 5, 3] : [6, 4, 5, 3, 2],
            'colors' => ['#184E83', '#1A6FC9', '#2ecc71', '#ffbe0b', '#ff4d6d'],
            'total_staff' => $params['building_id'] == 'all' ? 48 : 20,
            'maintenance' => $params['building_id'] == 'all' ? 18 : 6,
            'security' => $params['building_id'] == 'all' ? 12 : 4,
            'cleaning' => $params['building_id'] == 'all' ? 10 : 5,
            'admin' => $params['building_id'] == 'all' ? 5 : 3,
            'other' => $params['building_id'] == 'all' ? 3 : 2,
            'satisfaction' => $params['building_id'] == 'all' ? 84 : 82,
            'turnover' => $params['building_id'] == 'all' ? 12 : 10,
        ];

        return response()->json($data);
    }

    public function getMemberships(Request $request)
    {
        $params = $this->getFilterParams($request);

        $timeLabels = $this->getTimeLabels($params['range'], $params['start_date'] ?? null, $params['end_date'] ?? null);

        $activeData = array_map(function($i) { return 120 + ($i * 5); }, range(0, count($timeLabels) - 1));
        $expiredData = array_map(function() { return rand(35, 45); }, $timeLabels);

        $data = [
            'labels' => $timeLabels,
            'active' => $activeData,
            'expired' => $expiredData,
            'total' => $params['building_id'] == 'all' ? 185 : 65,
            'active_count' => end($activeData),
            'expired' => end($expiredData),
            'new_members' => $params['building_id'] == 'all' ? 24 : 8,
            'renewal_rate' => $params['building_id'] == 'all' ? 78 : 75,
            'growth' => $params['building_id'] == 'all' ? 15 : 12,
            'engagement' => $params['building_id'] == 'all' ? 72 : 70,
            'colors' => ['#184E83', '#ff4d6d']
        ];

        return response()->json($data);
    }

    public function getMaintenance(Request $request)
    {
        $params = $this->getFilterParams($request);

        $timeLabels = $this->getTimeLabels($params['range'], $params['start_date'] ?? null, $params['end_date'] ?? null);

        $completedData = array_map(function($i) { return 10 + ($i * 2); }, range(0, count($timeLabels) - 1));
        $pendingData = array_map(function() { return rand(5, 8); }, $timeLabels);
        $rejectedData = array_map(function() { return rand(1, 4); }, $timeLabels);

        $data = [
            'labels' => $timeLabels,
            'completed' => $completedData,
            'pending' => $pendingData,
            'rejected' => $rejectedData,
            'total' => array_sum($completedData) + array_sum($pendingData) + array_sum($rejectedData),
            'completed_count' => array_sum($completedData),
            'pending' => array_sum($pendingData),
            'rejected' => array_sum($rejectedData),
            'colors' => ['#2ecc71', '#ffbe0b', '#ff4d6d']
        ];

        return response()->json($data);
    }

    public function getTransactions(Request $request)
    {
        $params = $this->getFilterParams($request);
        $perPage = $request->input('per_page', 7);
        $page = $request->input('page', 1);

        // Simulated transaction data
        $transactions = [
            [
                'id' => 'TX-' . rand(100000, 999999),
                'title' => 'June Rent Payment',
                'unit' => 'Apt ' . rand(100, 500),
                'type' => 'Income',
                'status' => 'Completed',
                'amount' => rand(800, 1500),
                'date' => now()->subDays(rand(1, 30))->format('Y-m-d')
            ],
            // Add more simulated transactions...
        ];

        // Generate more transactions for pagination
        for ($i = 0; $i < 42; $i++) {
            $type = rand(0, 1) ? 'Income' : 'Expense';
            $statuses = ['Completed', 'Pending', 'Rejected'];

            $transactions[] = [
                'id' => 'TX-' . rand(100000, 999999),
                'title' => $type == 'Income' ?
                    ['Rent Payment', 'Parking Fee', 'Amenity Fee', 'Service Charge'][rand(0, 3)] :
                    ['Maintenance', 'Utilities', 'Staff Salary', 'Insurance'][rand(0, 3)],
                'unit' => $type == 'Income' ? 'Apt ' . rand(100, 500) : ['Common Area', 'Building', 'Parking Lot'][rand(0, 2)],
                'type' => $type,
                'status' => $statuses[rand(0, 2)],
                'amount' => $type == 'Income' ? rand(50, 1500) : rand(100, 2000),
                'date' => now()->subDays(rand(1, 90))->format('Y-m-d')
            ];
        }

        // Sort by date
        usort($transactions, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Paginate the results
        $total = count($transactions);
        $offset = ($page - 1) * $perPage;
        $paginated = array_slice($transactions, $offset, $perPage);

        return response()->json([
            'current_page' => $page,
            'data' => $paginated,
            'from' => $offset + 1,
            'to' => $offset + count($paginated),
            'total' => $total,
            'per_page' => $perPage,
            'last_page' => ceil($total / $perPage)
        ]);
    }

    private function getFilterParams(Request $request)
    {
        return [
            'building_id' => $request->input('building_id', 'all'),
            'range' => $request->input('range', '30days'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date')
        ];
    }

    private function getTimeLabels($range, $startDate = null, $endDate = null)
    {
        if ($range === 'custom' && $startDate && $endDate) {
            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);
            $diff = $start->diff($end);
            $days = $diff->days;

            if ($days > 60) {
                // Monthly labels
                $labels = [];
                $current = clone $start;
                $monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                while ($current <= $end) {
                    $labels[] = $monthNames[$current->format('n') - 1] . ' ' . $current->format('Y');
                    $current->add(new \DateInterval('P1M'));
                }

                return $labels;
            } elseif ($days > 14) {
                // Weekly labels
                $weeks = ceil($days / 7);
                return array_map(function($i) { return 'Week ' . ($i + 1); }, range(0, $weeks - 1));
            } else {
                // Daily labels
                $labels = [];
                $current = clone $start;

                while ($current <= $end) {
                    $labels[] = $current->format('M j');
                    $current->add(new \DateInterval('P1D'));
                }

                return $labels;
            }
        }

        // Default ranges
        switch ($range) {
            case '7days':
                return ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'];
            case '30days':
                return ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            case '90days':
                return ['Month 1', 'Month 2', 'Month 3'];
            default:
                return ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
        }
    }

}
