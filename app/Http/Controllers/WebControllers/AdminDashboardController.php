<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AdminFiltersService;
use Error;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    // Note: Month should be in YYYY-MM format in all month filters & year must be in YYYY
    public function index()
    {
        try {
            $adminService = new AdminFiltersService();
            $plans = $adminService->plans();
            $organizations = $adminService->organizations();
            return view('Heights.Admin.Dashboard.admin_dashboard', compact('plans', 'organizations'));
        } catch (\Throwable $e) {
            Log::error('Error in Admin Dashboard' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }


    // Month Filter
    public function getMonthlyStats(Request $request)
    {
        $month = $request->input('month');
        $start = $month ? Carbon::parse($month)->startOfMonth() : now()->startOfMonth();
        $end = $month ? Carbon::parse($month)->endOfMonth() : now()->endOfMonth();


        $totalOrganizations = Organization::count();
        $newOrganizationsThisMonth = Organization::whereBetween('created_at', [$start, $end])->count();
        $orgProgress = $totalOrganizations > 0 ? round(($newOrganizationsThisMonth / $totalOrganizations) * 100, 1) : 0;

        $totalUsers = User::count();
        $activeUsersThisMonth = User::whereBetween('last_login', [$start, $end])->count();
        $userProgress = $totalUsers > 0 ? round(($activeUsersThisMonth / $totalUsers) * 100, 1) : 0;

        $totalPendingBuildings = Building::whereIn('status', ['Under Review', 'For Re-Approval'])->count();
        $pendingBuildingsThisMonth = Building::whereIn('status', ['Under Review', 'For Re-Approval'])
            ->whereBetween('review_submitted_at', [$start, $end])
            ->count();
        $buildingProgress = $totalPendingBuildings > 0 ? round(($pendingBuildingsThisMonth / $totalPendingBuildings) * 100, 1) : 0;

        $currentMonthRevenue = Transaction::where('seller_type', 'platform')
            ->where('status', 'Completed')
            ->whereBetween('created_at', [$start, $end])
            ->sum('price');

        $previousStart = Carbon::parse($start)->subMonthNoOverflow()->startOfMonth();
        $previousEnd = Carbon::parse($start)->subMonthNoOverflow()->endOfMonth();

        $previousMonthRevenue = Transaction::where('seller_type', 'platform')
            ->where('status', 'Completed')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('price');

        $revenueGrowth = $previousMonthRevenue == 0
            ? ($currentMonthRevenue > 0 ? 100 : 0)
            : round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1);

        return response()->json([
            'counts' => [
                'totalOrganizations' => $totalOrganizations,
                'newOrganizationsThisMonth' => $newOrganizationsThisMonth,
                'totalUsers' => $totalUsers,
                'activeUsersThisMonth' => $activeUsersThisMonth,
                'totalPendingBuildings' => $totalPendingBuildings,
                'pendingBuildingsThisMonth' => $pendingBuildingsThisMonth,
            ],
            'revenue' => [
                'currentMonth' => $currentMonthRevenue,
                'previousMonth' => $previousMonthRevenue,
                'growth' => $revenueGrowth
            ],
            'progress' => [
                'organization' => $orgProgress,
                'user' => $userProgress,
                'building' => $buildingProgress,
            ]
        ]);
    }


    // Plan & Month filter
    // Note: Treating 'Canceled' as active until 'ends_at' passes
    public function getSubscriptionPlans(Request $request)
    {
        try {
            $plan_id = $request->input('plan');
            $month = $request->input('month');

            $start = $month ? Carbon::parse($month)->startOfMonth() : now()->startOfMonth();
            $end = $month ? Carbon::parse($month)->endOfMonth() : now()->endOfMonth();
            $days = $start->diffInDays($end) + 1;

            $previousStart = $start->copy()->subDays($days);
            $previousEnd = $start->copy()->subDay();

            $active = Subscription::where('subscription_status', '!=', 'Trial')
                ->where('created_at', '<=', $end)
                ->where('ends_at', '>=', $start)
                ->where('source_name', 'plan')
                ->when($plan_id, fn($q) => $q->where('source_id', $plan_id))
                ->count();

            $activePrev = Subscription::where('subscription_status', '!=', 'Trial')
                ->where('created_at', '<=', $previousEnd)
                ->where('ends_at', '>=', $previousStart)
                ->where('source_name', 'plan')
                ->when($plan_id, fn($q) => $q->where('source_id', $plan_id))
                ->count();

            $trial = Subscription::where('subscription_status', 'Trial')
                ->where('created_at', '<=', $end)
                ->where('ends_at', '>=', $start)
                ->where('source_name', 'plan')
                ->when($plan_id, fn($q) => $q->where('source_id', $plan_id))
                ->count();

            $trialPrev = Subscription::where('subscription_status', 'Trial')
                ->where('created_at', '<=', $previousEnd)
                ->where('ends_at', '>=', $previousStart)
                ->where('source_name', 'plan')
                ->when($plan_id, fn($q) => $q->where('source_id', $plan_id))
                ->count();

            $expired = Subscription::where('subscription_status', 'Expired')
                ->whereBetween('ends_at', [$start, $end])
                ->where('source_name', 'plan')
                ->when($plan_id, fn($q) => $q->where('source_id', $plan_id))
                ->count();

            $expiredPrev = Subscription::where('subscription_status', 'Expired')
                ->whereBetween('ends_at', [$previousStart, $previousEnd])
                ->where('source_name', 'plan')
                ->when($plan_id, fn($q) => $q->where('source_id', $plan_id))
                ->count();

            $getGrowth = function ($current, $previous) {
                if ($previous == 0 && $current == 0) return 0;
                if ($previous == 0) return 100;
                return round((($current - $previous) / $previous) * 100, 1);
            };

            return response()->json([
                'active' => $active,
                'activeTrials' => $trial,
                'expired' => $expired,
                'growth' => [
                    'active' => $getGrowth($active, $activePrev),
                    'activeTrials' => $getGrowth($trial, $trialPrev),
                    'expired' => $getGrowth($expired, $expiredPrev),
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    // organization & month filter
    public function getApprovalRequests(Request $request)
    {
        try {
            $organization_id = $request->input('organization');
            $month = $request->input('month');

            $start = $month ? Carbon::parse($month)->startOfMonth() : now()->startOfMonth();
            $end = $month ? Carbon::parse($month)->endOfMonth() : now()->endOfMonth();

            $prevStart = $start->copy()->subMonthNoOverflow()->startOfMonth();
            $prevEnd = $start->copy()->subMonthNoOverflow()->endOfMonth();

            $current = [
                'pending' => Building::whereIn('status', ['Under Review', 'For Re-Approval'])
                    ->whereBetween('review_submitted_at', [$start, $end])
                    ->when($organization_id, fn($q) => $q->where('organization_id', $organization_id))
                    ->count(),

                'approved' => Building::where('status', 'Approved')
                    ->whereBetween('approved_at', [$start, $end])
                    ->when($organization_id, fn($q) => $q->where('organization_id', $organization_id))
                    ->count(),

                'rejected' => Building::where('status', 'Rejected')
                    ->whereBetween('rejected_at', [$start, $end])
                    ->when($organization_id, fn($q) => $q->where('organization_id', $organization_id))
                    ->count(),
            ];

            $previous = [
                'pending' => Building::whereIn('status', ['Under Review', 'For Re-Approval'])
                    ->whereBetween('review_submitted_at', [$prevStart, $prevEnd])
                    ->when($organization_id, fn($q) => $q->where('organization_id', $organization_id))
                    ->count(),

                'approved' => Building::where('status', 'Approved')
                    ->whereBetween('approved_at', [$prevStart, $prevEnd])
                    ->when($organization_id, fn($q) => $q->where('organization_id', $organization_id))
                    ->count(),

                'rejected' => Building::where('status', 'Rejected')
                    ->whereBetween('rejected_at', [$prevStart, $prevEnd])
                    ->when($organization_id, fn($q) => $q->where('organization_id', $organization_id))
                    ->count(),
            ];

            $growth = [
                'pending' => $previous['pending'] == 0 ? null :
                    round((($current['pending'] - $previous['pending']) / $previous['pending']) * 100, 2),

                'approved' => $previous['approved'] == 0 ? null :
                    round((($current['approved'] - $previous['approved']) / $previous['approved']) * 100, 2),

                'rejected' => $previous['rejected'] == 0 ? null :
                    round((($current['rejected'] - $previous['rejected']) / $previous['rejected']) * 100, 2),
            ];

            return response()->json([
                'counts' => $current,
                'growth' => $growth,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    // plan & year filter
    public function getRevenueGrowth(Request $request)
    {
        try {
            $year = $request->input('year', now()->year);
            $plan_id = $request->input('plan');

            $prevDecStart = Carbon::create($year - 1, 12)->startOfMonth();
            $prevDecEnd = Carbon::create($year - 1, 12)->endOfMonth();

            $prevDecemberRevenue = Transaction::where('status', 'Completed')
                ->when($plan_id, fn($q) => $q->where('plan_id', $plan_id))
                ->whereBetween('created_at', [$prevDecStart, $prevDecEnd])
                ->where('seller_type', 'platform')
                ->sum('price');

            $revenues = [];

            foreach (range(1, 12) as $month) {
                $start = Carbon::create($year, $month, 1)->startOfMonth();
                $end = Carbon::create($year, $month, 1)->endOfMonth();

                $total = Transaction::where('status', 'Completed')
                    ->when($plan_id, fn($q) => $q->where('plan_id', $plan_id))
                    ->whereBetween('created_at', [$start, $end])
                    ->where('seller_type', 'platform')
                    ->sum('price');

                $revenues[] = $total;
            }

            $allRevenues = array_merge([$prevDecemberRevenue], $revenues);

            $growthRate = [];
            for ($i = 1; $i < count($allRevenues); $i++) {
                $prev = $allRevenues[$i - 1];
                $curr = $allRevenues[$i];
                $rate = $prev == 0 ? 0 : round((($curr - $prev) / $prev) * 100, 1);
                $growthRate[] = $rate;
            }

            $labels = collect(range(1, 12))
                ->map(fn($m) => Carbon::create()->month($m)->format('M'))
                ->toArray();

            return response()->json([
                'labels' => $labels,
                'revenue' => $revenues,
                'growthRate' => $growthRate
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    // start & end filter
    public function getPlanPopularity(Request $request)
    {
        try {
            $start = $request->input('start') ? Carbon::parse($request->input('start'))->startOfDay() : now()->subDays(30)->startOfDay();
            $end = $request->input('end') ? Carbon::parse($request->input('end'))->endOfDay() : now()->endOfDay();

            if ($start->gt($end)) {
                return response()->json([
                    'error' => 'Start date cannot be after end date.'
                ], 422);
            }

            $plans = Plan::select('id', 'name')->get();

            if ($plans->isEmpty()) {
                return response()->json([
                    'labels' => [],
                    'values' => [],
                    'data' => [
                        'datasets' => [[
                            'data' => []
                        ]]
                    ]
                ]);
            }

            $labels = $plans->pluck('name')->toArray();

            $values = $plans->map(function ($plan) use ($start, $end) {
                return Subscription::where('source_name', 'plan')
                    ->where('source_id', $plan->id)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
            })->toArray();

            return response()->json([
                'labels' => $labels,
                'values' => $values,
                'data' => [
                    'datasets' => [[
                        'data' => $values,
                    ]]
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    // plan & year filter
    public function getSubscriptionDistribution(Request $request)
    {
        try {
            $year = $request->input('year', now()->year);
            $plan_id = $request->input('plan');

            $subscriptions = Subscription::whereYear('updated_at', $year)
                ->where('source_name', 'plan')
                ->whereIn('subscription_status', ['Expired', 'Canceled'])
                ->when($plan_id, fn($q) => $q->where('source_id', $plan_id))
                ->select('id', 'subscription_status', 'updated_at')
                ->get();

            $labels = collect(range(1, 12))
                ->map(fn($m) => Carbon::create()->month($m)->format('M'))
                ->toArray();

            $active = [];
            $expired = [];
            $canceled = [];

            foreach (range(1, 12) as $month) {
                $active[] = Subscription::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('source_name', 'plan')
                    ->when($plan_id, fn($q) => $q->where('source_id', $plan_id))
                    ->count();

                $monthly = $subscriptions->filter(fn($sub) => Carbon::parse($sub->updated_at)->month == $month);
                $expired[] = $monthly->where('subscription_status', 'Expired')->count();
                $canceled[] = $monthly->where('subscription_status', 'Canceled')->count();
            }

            return response()->json([
                'labels' => $labels,
                'active' => $active,
                'expired' => $expired,
                'canceled' => $canceled,
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
                        'label' => 'Canceled',
                        'data' => $canceled,
                        'backgroundColor' => 'rgba(255, 205, 86, 0.7)'
                    ]
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    // year filter
    public function getApprovalTimeline(Request $request)
    {
        try {
            $year = $request->input('year', now()->year);

            $months = collect(range(1, 12))
                ->map(fn($m) => Carbon::create()->month($m)->format('M'));

            $records = Building::whereYear('review_submitted_at', $year)->get();

            $grouped = $records->groupBy(fn($item) => Carbon::parse($item->review_submitted_at)->month);

            $pending = [];
            $approved = [];
            $rejected = [];
            $labels = [];

            foreach (range(1, 12) as $month) {
                $labels[] = $months[$month - 1];
                $monthRecords = $grouped->get($month, collect());

                $pending[] = $monthRecords->whereIn('status', ['Under Review', 'For Re-Approval'])->count();
                $approved[] = $monthRecords->where('status', 'Approved')->count();
                $rejected[] = $monthRecords->where('status', 'Rejected')->count();
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
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
