<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    //
    public function index(){

        return view('Heights.Admin.Dashboard.admin_dashboard');

    }

    public function data()
    {
        $buildings = Building::count();
        $organizations = Organization::count();
        $owners = User::where('role_id',2)->count();
        $buildingsForApproval = Building::whereIn('status', ['Under Review', 'Reapproved'])->count();

        return response()->json([
            'counts' => [
                'buildings' => $buildings,
                'organizations' => $organizations,
                'owners' => $owners,
                'buildingsForApproval' => $buildingsForApproval,
            ],
        ]);
    }

    protected function applyDateRange($data, $range, $customStart = null, $customEnd = null)
    {
        $now = Carbon::now();
        $filteredData = $data;

        if ($range === '7days') {
            $filteredData = array_slice($data, -7);
        } elseif ($range === '30days') {
            $filteredData = array_slice($data, -12); // Last 30 days (using months for sample)
        } elseif ($range === '90days') {
            $filteredData = array_slice($data, -3); // Last 3 months
        } elseif ($range === 'custom' && $customStart && $customEnd) {
            // For sample data, we'll just return a portion
            $filteredData = array_slice($data, 3, 6); // Middle portion for demo
        }

        return $filteredData;
    }

    public function getStats(Request $request)
    {
        // Simulate filtered counts based on request
        $range = $request->input('range', 'all');

        $data = [
            'totalOrganizations' => $range === '7days' ? 15 : ($range === '30days' ? 45 : 125),
            'totalOwners' => $range === '7days' ? 32 : ($range === '30days' ? 142 : 342),
            'pendingApprovals' => $range === '7days' ? 3 : ($range === '30days' ? 8 : 18),
            'totalRevenue' => $range === '7days' ? 5250 : ($range === '30days' ? 18750 : 48750)
        ];

        return response()->json($data);
    }

    public function getSubscriptionPlans(Request $request)
    {
        $range = $request->input('range', '30days');
        $planType = $request->input('plantype', 'all');

        // Base data
        $data = [
            'active' => 89,
            'expired' => 12,
            'trial' => 23,
            'trend' => [
                'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                'values' => [12, 19, 15, 22]
            ]
        ];

        // Apply "filters" to sample data
        if ($planType !== 'all') {
            $data['active'] = round($data['active'] * ($planType === 'premium' ? 0.3 : 0.7));
            $data['expired'] = round($data['expired'] * ($planType === 'premium' ? 0.2 : 0.8));
            $data['trial'] = round($data['trial'] * ($planType === 'premium' ? 0.1 : 0.9));
        }

        if ($range === '7days') {
            $data['trend']['values'] = array_slice($data['trend']['values'], -2);
            $data['trend']['labels'] = array_slice($data['trend']['labels'], -2);
        }

        return response()->json($data);
    }

    public function getApprovalRequests(Request $request)
    {
        $range = $request->input('range', '30days');
        $status = $request->input('status', 'all');
        $type = $request->input('type', 'all');

        $baseData = [
            'pending' => 18,
            'approved' => 156,
            'rejected' => 9
        ];

        // Apply type filter simulation
        if ($type !== 'all') {
            $multiplier = match($type) {
                'building' => 0.6,
                'organization' => 0.3,
                'user' => 0.1,
                default => 1
            };

            foreach ($baseData as $key => $value) {
                $baseData[$key] = round($value * $multiplier);
            }
        }

        // Apply status filter simulation
        if ($status !== 'all') {
            $filteredData = [
                'pending' => $status === 'pending' ? $baseData['pending'] : 0,
                'approved' => $status === 'approved' ? $baseData['approved'] : 0,
                'rejected' => $status === 'rejected' ? $baseData['rejected'] : 0
            ];

            return response()->json($filteredData);
        }

        // Apply date range simulation
        if ($range === '7days') {
            foreach ($baseData as $key => $value) {
                $baseData[$key] = round($value * 0.25);
            }
        } elseif ($range === '90days') {
            foreach ($baseData as $key => $value) {
                $baseData[$key] = round($value * 1.5);
            }
        }

        return response()->json($baseData);
    }

    public function getRevenueGrowth(Request $request)
    {
        $range = $request->input('range', '12months');
        $revenueType = $request->input('revenuetype', 'all');

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $revenue = [12000, 19000, 15000, 18000, 21000, 25000, 22000, 24000, 23000, 26000, 28000, 30000];
        $growthRate = [0, 58.3, -21.1, 20, 16.7, 19, -12, 9.1, -4.2, 13, 7.7, 7.1];

        // Apply date range
        if ($range === '3months') {
            $labels = array_slice($labels, -3);
            $revenue = array_slice($revenue, -3);
            $growthRate = array_slice($growthRate, -3);
        } elseif ($range === '6months') {
            $labels = array_slice($labels, -6);
            $revenue = array_slice($revenue, -6);
            $growthRate = array_slice($growthRate, -6);
        }

        // Apply revenue type simulation
        if ($revenueType !== 'all') {
            $multiplier = match($revenueType) {
                'subscription' => 0.7,
                'service' => 0.2,
                'other' => 0.1,
                default => 1
            };

            $revenue = array_map(fn($val) => round($val * $multiplier), $revenue);
        }

        return response()->json([
            'labels' => $labels,
            'revenue' => $revenue,
            'growthRate' => $growthRate
        ]);
    }

    public function getPlanPopularity(Request $request)
    {
        $range = $request->input('range', '30days');
        $status = $request->input('status', 'all');

        $labels = ['Basic', 'Standard', 'Premium', 'Enterprise'];
        $values = [35, 45, 15, 5];

        // Apply status filter simulation
        if ($status !== 'all') {
            $multiplier = match($status) {
                'active' => [0.8, 0.9, 0.7, 0.6],
                'expired' => [0.1, 0.05, 0.2, 0.3],
                'trial' => [0.1, 0.05, 0.1, 0.1],
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
            'values' => $values,
            'data' => [
                'datasets' => [[
                    'data' => $values,
                    'backgroundColor' => ['#36A2EB', '#4BC0C0', '#FFCE56', '#FF6384']
                ]]
            ]
        ]);
    }

    public function getSubscriptionDistribution(Request $request)
    {
        $range = $request->input('range', '12months');
        $planType = $request->input('plantype', 'all');

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $active = [45, 60, 55, 70, 85, 90, 80, 75, 82, 88, 92, 95];
        $expired = [15, 10, 15, 10, 5, 5, 10, 15, 8, 7, 3, 2];
        $trial = [5, 8, 12, 15, 18, 20, 15, 12, 10, 8, 5, 3];

        // Apply date range
        if ($range === '3months') {
            $labels = array_slice($labels, -3);
            $active = array_slice($active, -3);
            $expired = array_slice($expired, -3);
            $trial = array_slice($trial, -3);
        } elseif ($range === '6months') {
            $labels = array_slice($labels, -6);
            $active = array_slice($active, -6);
            $expired = array_slice($expired, -6);
            $trial = array_slice($trial, -6);
        }

        // Apply plan type simulation
        if ($planType !== 'all') {
            $multiplier = match($planType) {
                'basic' => [0.9, 0.1, 0.1],
                'standard' => [0.1, 0.8, 0.1],
                'premium' => [0.05, 0.1, 0.8],
                'enterprise' => [0.05, 0.05, 0.9],
                default => [1, 1, 1]
            };

            $active = array_map(fn($val) => round($val * $multiplier[0]), $active);
            $expired = array_map(fn($val) => round($val * $multiplier[1]), $expired);
            $trial = array_map(fn($val) => round($val * $multiplier[2]), $trial);
        }

        return response()->json([
            'labels' => $labels,
            'active' => $active,
            'expired' => $expired,
            'trial' => $trial,
            'datasets' => [
                [
                    'label' => 'Active',
                    'data' => $active,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.7)'
                ],
                [
                    'label' => 'Expired',
                    'data' => $expired,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.7)'
                ],
                [
                    'label' => 'Trial',
                    'data' => $trial,
                    'backgroundColor' => 'rgba(255, 205, 86, 0.7)'
                ]
            ]
        ]);
    }

    public function getApprovalTimeline(Request $request)
    {
        $range = $request->input('range', '12months');
        $requestType = $request->input('requesttype', 'all');

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $pending = [10, 15, 12, 8, 5, 7, 12, 15, 18, 20, 15, 10];
        $approved = [20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75];
        $rejected = [5, 3, 8, 5, 2, 3, 5, 8, 5, 3, 2, 1];

        // Apply date range
        if ($range === '3months') {
            $labels = array_slice($labels, -3);
            $pending = array_slice($pending, -3);
            $approved = array_slice($approved, -3);
            $rejected = array_slice($rejected, -3);
        } elseif ($range === '6months') {
            $labels = array_slice($labels, -6);
            $pending = array_slice($pending, -6);
            $approved = array_slice($approved, -6);
            $rejected = array_slice($rejected, -6);
        }

        // Apply request type simulation
        if ($requestType !== 'all') {
            $multiplier = match($requestType) {
                'building' => [0.7, 0.6, 0.5],
                'organization' => [0.2, 0.3, 0.3],
                'user' => [0.1, 0.1, 0.2],
                default => [1, 1, 1]
            };

            $pending = array_map(fn($val) => round($val * $multiplier[0]), $pending);
            $approved = array_map(fn($val) => round($val * $multiplier[1]), $approved);
            $rejected = array_map(fn($val) => round($val * $multiplier[2]), $rejected);
        }

        return response()->json([
            'labels' => $labels,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'datasets' => [
                [
                    'label' => 'Pending',
                    'data' => $pending,
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.1)'
                ],
                [
                    'label' => 'Approved',
                    'data' => $approved,
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.1)'
                ],
                [
                    'label' => 'Rejected',
                    'data' => $rejected,
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.1)'
                ]
            ]
        ]);
    }

}
