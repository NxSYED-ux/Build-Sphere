<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BuildingUnit;

class HomePageController extends Controller
{
    public function homePage(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json(['error' => 'User ID is required'], 400);
            }

            $user = User::select('name', 'picture')->find($request->user()->id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $search = $request->query('search');
            $minPrice = $request->query('minPrice');
            $maxPrice = $request->query('maxPrice');
            $unitType = $request->query('unitType');
            $saleOrRent = $request->query('saleOrRent');
            $city = $request->query('city');
            $limit = $request->query('limit', 20);
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

            $availableUnits = $query->with([
                'level' => function ($query) {
                    $query->select('id', 'level_name')
                        ->with([
                            'building' => function ($query) {
                                $query->select('id', 'name')
                                    ->with([
                                        'address' => function ($query) {
                                            $query->select('id', 'location', 'city', 'province', 'country');
                                        }
                                    ]);
                            }
                        ]);
                },
                'pictures' => function ($query) {
                    $query->select('id', 'file_path');
                }
            ])->select('id', 'unit_name', 'unit_type', 'price', 'sale_or_rent', 'availability_status', 'updated_at')
                ->orderBy('updated_at', 'DESC')
                ->paginate($limit);

            return response()->json([
                'user' => $user,
                'units' => $availableUnits,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage() ?: 'An error occurred while fetching home page data.'], 500);
        }
    }

}

