<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OwnerDashboardController extends Controller
{
    //
    public function index(){

        return view('Heights.Owner.Dashboard.owner_dashboard');

    }

    protected function parseDateRange($range)
    {
        $now = Carbon::now();

        switch ($range) {
            case '7days':
                return [$now->copy()->subDays(7), $now];
            case '30days':
                return [$now->copy()->subDays(30), $now];
            case '90days':
                return [$now->copy()->subDays(90), $now];
            case 'custom':
                // Will be handled by the specific methods
                return null;
            default:
                return [$now->copy()->startOfMonth(), $now];
        }
    }

    // Get dashboard stats
    public function getStats(Request $request)
    {
        $range = $request->input('range', 'all');

        $data = [
            'totalBuildings' => $range === '7days' ? 2 : ($range === '30days' ? 5 : 12),
            'totalUnits' => $range === '7days' ? 15 : ($range === '30days' ? 45 : 120),
            'totalStaff' => $range === '7days' ? 5 : ($range === '30days' ? 15 : 30),
            'totalRevenue' => $range === '7days' ? 5250 : ($range === '30days' ? 18750 : 48750),
            'totalExpense' => $range === '7days' ? 3200 : ($range === '30days' ? 12500 : 32500),
            'netProfit' => $range === '7days' ? 2050 : ($range === '30days' ? 6250 : 16250)
        ];

        return response()->json($data);
    }

    // Get unit occupancy data
    public function getUnitOccupancy(Request $request)
    {
        $range = $request->input('range', '30days');
        $buildingId = $request->input('building', 'all');

        // Base data
        $data = [
            'rented' => 75,
            'sold' => 15,
            'available' => 30,
            'trend' => [
                'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                'values' => [15, 22, 18, 25]
            ]
        ];

        // Apply building filter simulation
        if ($buildingId !== 'all') {
            $multiplier = match($buildingId) {
                'building1' => 0.4,
                'building2' => 0.3,
                'building3' => 0.3,
                default => 1
            };

            $data['rented'] = round($data['rented'] * $multiplier);
            $data['sold'] = round($data['sold'] * $multiplier);
            $data['available'] = round($data['available'] * $multiplier);

            foreach ($data['trend']['values'] as &$value) {
                $value = round($value * $multiplier);
            }
        }

        // Apply date range simulation
        if ($range === '7days') {
            $data['trend']['values'] = array_slice($data['trend']['values'], -2);
            $data['trend']['labels'] = array_slice($data['trend']['labels'], -2);
        } elseif ($range === '90days') {
            $data['rented'] = round($data['rented'] * 1.5);
            $data['sold'] = round($data['sold'] * 1.5);
            $data['available'] = round($data['available'] * 1.5);
        }

        return response()->json($data);
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

    // Get unit status data
    public function getUnitStatus(Request $request)
    {
        $range = $request->input('range', '12months');
        $buildingId = $request->input('building', 'all');

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $rented = [25, 30, 28, 35, 40, 45, 42, 40, 43, 46, 48, 50];
        $sold = [10, 8, 12, 10, 7, 5, 8, 10, 7, 5, 3, 2];
        $available = [15, 12, 10, 5, 3, 0, 0, 0, 0, 0, 0, 0];

        // Apply building filter simulation
        if ($buildingId !== 'all') {
            $multiplier = match($buildingId) {
                'building1' => 0.4,
                'building2' => 0.3,
                'building3' => 0.3,
                default => 1
            };

            $rented = array_map(fn($val) => round($val * $multiplier), $rented);
            $sold = array_map(fn($val) => round($val * $multiplier), $sold);
            $available = array_map(fn($val) => round($val * $multiplier), $available);
        }

        // Apply date range
        if ($range === '3months') {
            $labels = array_slice($labels, -3);
            $rented = array_slice($rented, -3);
            $sold = array_slice($sold, -3);
            $available = array_slice($available, -3);
        } elseif ($range === '6months') {
            $labels = array_slice($labels, -6);
            $rented = array_slice($rented, -6);
            $sold = array_slice($sold, -6);
            $available = array_slice($available, -6);
        }

        return response()->json([
            'labels' => $labels,
            'rented' => $rented,
            'sold' => $sold,
            'available' => $available
        ]);
    }

    // Get staff distribution data
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

    // Get income vs expense data
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

    // Get membership plan usage data
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
}
