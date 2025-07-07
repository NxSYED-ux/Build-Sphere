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

    // API 1: Metrics data (Total Units, Total Levels, Total Income, Total Expenses)
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

    // API 2: Income/Expense data (Income vs Expense, Financial Summary, Income Sources, Expense Categories, Recent Transactions)
    public function getIncomeExpense(Request $request)
    {
        $buildingId = $request->input('building_id', 'all');
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $transactions = $this->generateDummyTransactions($request->building_id);

        // Dummy data
        $data = [
            'total_income' => $buildingId === 'all' ? 24580 : 8500,
            'total_expenses' => $buildingId === 'all' ? 8420 : 3200,
            'income_sources' => [
                'labels' => ['Rent', 'Parking', 'Amenities', 'Other'],
                'data' => $buildingId === 'all' ? [18000, 4200, 1500, 880] : [6000, 1500, 700, 300],
                'colors' => ['#184E83', '#1A6FC9', '#2ecc71', '#ffbe0b']
            ],
            'expense_categories' => [
                'labels' => ['Maintenance', 'Utilities', 'Staff', 'Insurance', 'Other'],
                'data' => $buildingId === 'all' ? [3200, 2800, 1500, 500, 420] : [1200, 1000, 600, 200, 200],
                'colors' => ['#ff4d6d', '#ff758f', '#ff8fa3', '#ffb3c1', '#ffccd5']
            ],
            'recent_transactions' => $transactions
        ];

        return response()->json($data);
    }

    // API 3: Occupancy data
    public function getOccupancy(Request $request)
    {
        $buildingId = $request->input('building_id', 'all');
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Generate time labels based on date range
        $timeLabels = $this->generateTimeLabels($startDate, $endDate);

        // Dummy data
        $data = [
            'total_units' => $buildingId === 'all' ? 248 : 80,
            'rented_units' => $buildingId === 'all' ? 200 : 70,
            'sold_units' => $buildingId === 'all' ? 28 : 4,
            'available_units' => $buildingId === 'all' ? 20 : 6,
            'occupancy_trend' => [
                'labels' => $timeLabels,
                'available' => array_map(function() { return rand(5, 15); }, $timeLabels),
                'rented' => array_map(function() { return rand(150, 200); }, $timeLabels),
                'sold' => array_map(function() { return rand(5, 15); }, $timeLabels)
            ]
        ];

        return response()->json($data);
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

    // Helper function to generate dummy transactions
    private function generateDummyTransactions($buildingId)
    {
        $types = ['Income', 'Expense'];
        $statuses = ['completed', 'pending', 'rejected'];
        $incomeTitles = ['Rent Payment', 'Parking Fee', 'Amenity Fee', 'Service Charge'];
        $expenseTitles = ['Maintenance', 'Utilities', 'Staff Salary', 'Insurance'];
        $units = ['Apt 101', 'Apt 202', 'Apt 303', 'Common Area', 'Building'];

        $transactions = [];
        $count = $buildingId === 'all' ? 50 : 30; // More records for client-side pagination

        for ($i = 0; $i < $count; $i++) {
            $type = $types[array_rand($types)];
            $status = $statuses[array_rand($statuses)];
            $title = $type === 'Income'
                ? $incomeTitles[array_rand($incomeTitles)]
                : $expenseTitles[array_rand($expenseTitles)];

            $transactions[] = [
                'id' => 'TX-' . str_pad($i + 1000, 4, '0', STR_PAD_LEFT),
                'title' => $title,
                'unit' => $units[array_rand($units)],
                'type' => $type,
                'status' => ucfirst($status),
                'amount' => $type === 'Income' ? rand(50, 2000) : rand(100, 1500),
                'date' => Carbon::now()->subDays(rand(0, 30))->format('Y-m-d')
            ];
        }

        // Return just the data array without pagination info
        return $transactions;
    }

    //    unitssssssssssssssssssss
    public function getUnitDetails($unitId)
    {
        // In a real app, you would query your database here
        $data = [
            'id' => $unitId,
            'name' => "UNIT-$unitId",
            'building' => 'Downtown Tower',
            'type' => '2 Bedroom',
            'size' => '850 sq.ft',
            'status' => 'rented', // or 'sold'
            'tenant' => 'John Smith',
            'tenant_since' => 'May 2022',
            'purchase_price' => '$350,000',
            'market_value' => '$420,000',
            'image_url' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80'
        ];

        return response()->json($data);
    }

    // Get unit trends and metrics
    public function getUnitTrends($unitId)
    {
        $data = [
            'total_income' => '$2,400',
            'total_expenses' => '$850',
            'net_profit' => '$1,550',
            'occupancy_rate' => '92%',
            'income_change' => '12%',
            'expenses_change' => '5%',
            'profit_change' => '18%',
            'occupancy_change' => '3%'
        ];

        return response()->json($data);
    }

    // Get income vs expense data for charts
    public function getIncomeExpenseData($unitId)
    {
        $data = [
            'labels' => ['Income', 'Expenses'],
            'data' => [2400, 850],
            'colors' => ['#184E83', '#ff4d6d']
        ];

        return response()->json($data);
    }

    // Get income sources breakdown
    public function getUnitIncomeSources($unitId)
    {
        $data = [
            'labels' => ['Rent', 'Parking', 'Amenities'],
            'data' => [1200, 75, 1125],
            'colors' => ['#184E83', '#1A6FC9', '#2ecc71']
        ];

        return response()->json($data);
    }

    // Get expense categories breakdown
    public function getUnitExpenseSources($unitId)
    {
        $data = [
            'labels' => ['Maintenance', 'Utilities', 'Insurance', 'Other'],
            'data' => [450, 250, 100, 50],
            'colors' => ['#ff4d6d', '#ff758f', '#ff8fa3', '#ffb3c1']
        ];

        return response()->json($data);
    }

    // Get current rental contract details
    public function getCurrentContract($unitId)
    {
        $data = [
            'tenant_name' => 'John Smith',
            'start_date' => 'June 1, 2023',
            'end_date' => 'May 31, 2024',
            'monthly_rent' => '$1,200',
            'deposit' => '$1,200',
            'payment_status' => 'current',
            'lease_type' => 'Fixed Term',
            'tenant_details' => [
                'contact' => 'john.smith@example.com | (555) 123-4567',
                'emergency_contact' => 'Jane Smith (Spouse) - (555) 987-6543',
                'occupation' => 'Software Engineer at Tech Corp',
                'notes' => 'Prefers communication via email. Has one pet (cat).'
            ]
        ];

        return response()->json($data);
    }

    // Get maintenance data
    public function getMaintenanceData($unitId)
    {
        $data = [
            'active_requests' => [
                [
                    'id' => "MR-2023-$unitId-1",
                    'type' => 'Plumbing - Leaky faucet',
                    'reported' => 'June 15, 2023',
                    'status' => 'in_progress'
                ],
                [
                    'id' => "MR-2023-$unitId-2",
                    'type' => 'Electrical - Outlet not working',
                    'reported' => 'June 20, 2023',
                    'status' => 'pending'
                ]
            ],
            'history' => [
                'completed' => 8,
                'in_progress' => 2,
                'pending' => 2
            ],
            'chart_data' => [
                'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                'data' => [3, 5, 2, 4],
                'color' => '#184E83'
            ]
        ];

        return response()->json($data);
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

    // Get unit transaction history
    public function getTransactionHistory($unitId)
    {
        $data = [
            [
                'date' => 'June 1, 2023',
                'id' => "TXN-2023-$unitId-001",
                'description' => 'June Rent Payment',
                'type' => 'income',
                'amount' => '$1,200.00',
                'status' => 'completed'
            ],
            [
                'date' => 'June 15, 2023',
                'id' => "TXN-2023-$unitId-002",
                'description' => 'Plumbing Repair - Leaky faucet',
                'type' => 'expense',
                'amount' => '$85.00',
                'status' => 'pending'
            ],
            [
                'date' => 'June 1, 2023',
                'id' => "TXN-2023-$unitId-003",
                'description' => 'Parking Spot Rental',
                'type' => 'income',
                'amount' => '$75.00',
                'status' => 'completed'
            ],
            [
                'date' => 'May 1, 2023',
                'id' => "TXN-2023-$unitId-004",
                'description' => 'May Rent Payment',
                'type' => 'income',
                'amount' => '$1,200.00',
                'status' => 'completed'
            ],
            [
                'date' => 'April 15, 2023',
                'id' => "TXN-2023-$unitId-005",
                'description' => 'HVAC Maintenance',
                'type' => 'expense',
                'amount' => '$120.00',
                'status' => 'completed'
            ]
        ];

        return response()->json($data);
    }

}
