<?php

namespace App\Http\Controllers\AppControllers; 

use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\BuildingUnit;
use App\Models\UnitPicture;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class HomePageController extends Controller
{
    public function homePage(Request $request)
    {
        try {
            // Fetch user data
            $userData = User::select('name', 'picture')
                ->find($request->user()->id);

            if (!$userData) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Get query parameters
            $search = $request->query('search');
            $minPrice = $request->query('minPrice');
            $maxPrice = $request->query('maxPrice');
            $unitType = $request->query('unitType');
            $saleOrRent = $request->query('saleOrRent');
            $location = $request->query('location');
            $limit = $request->query('limit', 20);
            $offset = $request->query('offset', 0);
            $excludeUnit = $request->query('exclude_unit');

            // Build filters
            $filters = [
                'sale_or_rent' => ($saleOrRent && $saleOrRent !== 'any') ? $saleOrRent : ['!=', 'Not Available'],
                'availability_status' => 'Available',
                'status' => 'Approved',
            ];

            if ($excludeUnit) {
                $filters['id'] = ['!=', $excludeUnit];
            }

            if ($minPrice && $maxPrice) {
                $filters['price'] = ['between', [(float)$minPrice, (float)$maxPrice]];
            } elseif ($minPrice) {
                $filters['price'] = ['>=', (float)$minPrice];
            } elseif ($maxPrice) {
                $filters['price'] = ['<=', (float)$maxPrice];
            }

            if ($unitType) {
                $types = array_map('trim', explode(',', $unitType));
                $filters['unit_type'] = count($types) > 1 ? ['in', $types] : $types[0];
            }

            // Location filter
            $locationFilters = [];
            if ($location) {
                $locationFilters['location'] = ['like', "%$location%"];
            }

            // Search filter
            $searchFilter = [];
            if ($search) {
                $searchFilter = [
                    DB::raw('(
                        building.address.location LIKE ? OR 
                        building.address.city LIKE ? OR 
                        building.address.province LIKE ? OR 
                        building.address.country LIKE ? OR 
                        level.level_name LIKE ? OR 
                        building.name LIKE ? OR 
                        unit_name LIKE ?
                    )', [
                        "%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%"
                    ])
                ];
            }

            // Fetch available units with filters and relationships
            $availableUnits = BuildingUnit::with([
                'level' => function ($query) {
                    $query->select('level_name');
                },
                'level.building' => function ($query) use ($locationFilters) {
                    $query->select('name')
                        ->with([
                            'address' => function ($query) use ($locationFilters) {
                                $query->select('location', 'city', 'province', 'country')
                                    ->where($locationFilters);
                            }
                        ]);
                },
                'pictures' => function ($query) {
                    $query->select('file_path');
                },
            ])
                ->where($filters)
                ->where($searchFilter)
                ->select('id', 'unit_name', 'unit_type', 'price', 'sale_or_rent', 'availability_status', 'updated_at')
                ->limit($limit)
                ->offset($offset)
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'user' => $userData,
                'units' => $availableUnits
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error in homePage controller:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => $e->getMessage() ?: 'An error occurred while fetching home page data.'
            ], 500);
        }
    }
}
