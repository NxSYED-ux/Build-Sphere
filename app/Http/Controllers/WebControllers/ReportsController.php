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

}
