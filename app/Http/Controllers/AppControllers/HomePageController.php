<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BuildingUnit;
use Illuminate\Support\Facades\Log;

class HomePageController extends Controller
{
    public function homePage(Request $request)
    {
        try {
            $search = $request->query('search');
            $minPrice = $request->query('minPrice');
            $maxPrice = $request->query('maxPrice');
            $unitType = $request->query('unitType');
            $saleOrRent = $request->query('saleOrRent');
            $city = $request->query('city');
            $excludeUnit = $request->query('exclude_unit');

            $query = BuildingUnit::query();

            if ($saleOrRent) {
                $query->whereIn('sale_or_rent', explode(',', $saleOrRent));
            } else {
                $query->where('sale_or_rent', '<>', 'Not Available');
            }

            $query->where('availability_status', 'Available')
                ->where('status', 'Approved');

            if ($excludeUnit) {
                $query->where('id', '<>', (int) $excludeUnit);
            }

            if ($minPrice) {
                $query->where('price', '>=', (float) $minPrice);
            }
            if ($maxPrice) {
                $query->where('price', '<=', (float) $maxPrice);
            }

            if ($unitType) {
                $unitTypes = explode(',', $unitType);
                $query->whereIn('unit_type', $unitTypes);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('unit_name', 'like', "%{$search}%")
                        ->orWhereHas('level.building.address', function ($q) use ($search) {
                            $q->where('location', 'like', "%{$search}%")
                                ->orWhere('province', 'like', "%{$search}%");
                        })
                        ->orWhereHas('level', function ($q) use ($search) {
                            $q->where('level_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('level.building', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if ($city) {
                $query->whereHas('level.building.address', function ($q) use ($city) {
                    $q->where('city', '=', $city);
                });
            }

            $availableUnits = $query->whereHas('level')
            ->whereHas('level.building')
            ->whereHas('level.building.address')
            ->with([
                'level' => function ($query) {
                    $query->select('id', 'level_name', 'building_id')
                        ->with([
                            'building' => function ($query) {
                                $query->select('id', 'name', 'address_id')
                                    ->with('address');
                            }
                        ]);
                },
                'pictures' => function ($query) {
                    $query->select('unit_id', 'file_path');
                }
            ])->select('id', 'unit_name', 'unit_type', 'price', 'area', 'sale_or_rent', 'availability_status', 'level_id')
                ->orderBy('updated_at', 'DESC')
                ->get();

            return response()->json([
                'units' => $availableUnits,
            ]);

        } catch (\Exception $e) {
            Log::error("Error in HomePage : " . $e->getMessage());
            return response()->json(['error' => $e->getMessage() ?: 'An error occurred while fetching home page data.'], 500);
        }
    }
}
