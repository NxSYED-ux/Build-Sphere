<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\BuildingUnit;
use Illuminate\Support\Facades\Log;

class ListingController extends Controller
{
    // Home Page
    public function homePageListings(Request $request)
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

            $availableUnits = $query
                ->whereHas('level')
                ->whereHas('level.building', function ($buildingQuery) {
                    $buildingQuery->whereIn('status', ['Approved', 'For Re-Approval'])
                                    ->where('isFreeze', 0);
                })
                ->whereHas('level.building.address')
                ->with([
                    'level' => function ($query) {
                        $query->select('id', 'level_name', 'building_id')
                            ->with([
                                'building' => function ($query) {
                                    $query->select('id', 'name', 'address_id', 'status')
                                        ->with('address:id,location,city,province,country');
                                }
                            ]);
                    },
                    'pictures' => function ($query) {
                        $query->select('unit_id', 'file_path');
                    }
                ])
                ->select('id', 'unit_name', 'unit_type', 'price', 'area', 'sale_or_rent', 'availability_status', 'level_id')
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


    // Unit Details
    public function unitDetails($id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json(['error' => 'Invalid unit ID'], 400);
            }

            $unitDetails = BuildingUnit::where('id', $id)
                ->where('availability_status', 'Available')
                ->where('status', 'Approved')
                ->select('id', 'unit_name', 'unit_type', 'price', 'area', 'sale_or_rent', 'description', 'level_id', 'organization_id')
                ->whereHas('level.building', function ($query) {
                    $query->whereIn('status', ['Approved', 'For Re-Approval'])
                            ->where('isFreeze', 0);
                })
                ->with([
                    'level:id,building_id,level_name',
                    'level.building:id,name,address_id',
                    'level.building.address:id,location,city,province,country',
                    'pictures:unit_id,file_path',
                    'organization:id,name,is_online_payment_enabled',
                    'organization.pictures:organization_id,file_path'
                ])
                ->first();

            if (!$unitDetails) {
                return response()->json(['error' => 'This unit is no longer available.'], 410);
            }

            return response()->json([ 'unitDetails' => $unitDetails ]);
        } catch (\Exception $e) {
            Log::error("Error in unitDetails: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching details page data.'], 500);
        }
    }


    // Organization Buildings
    public function organizationWithBuildings($id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json(['error' => 'Invalid organization ID'], 400);
            }

            $organization = Organization::where('id', $id)
                ->select('id', 'name')
                ->with(['pictures' => function ($query) {
                    $query->select('organization_id', 'file_path');
                }])
                ->first();

            if (!$organization) {
                return response()->json(['error' => 'Organization not found'], 404);
            }

            $buildings = Building::where('organization_id', $id)
                ->whereIn('status', ['Approved', 'For Re-Approval'])
                ->where('isFreeze', 0)
                ->select('id', 'name', 'address_id')
                ->with([
                    'address:id,location,city,province,country',
                    'pictures:building_id,file_path'
                ])
                ->orderBy('updated_at', 'DESC')
                ->get();

            return response()->json([
                'organization' => $organization,
                'buildings' => $buildings,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching organization details: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching organization details.'], 500);
        }
    }


    // Building Units
    public function specificBuildingUnits($id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json(['error' => 'Invalid building ID'], 400);
            }

            $building = Building::where('id', $id)
                ->whereIn('status', ['Approved', 'For Re-Approval'])
                ->where('isFreeze', 0)
                ->select('id', 'name', 'address_id')
                ->with([
                    'address:id,location,city,province,country',
                    'levels' => function ($levelQuery) {
                        $levelQuery->select('id', 'building_id', 'level_name')
                            ->whereHas('units', function ($unitQuery) {
                                $unitQuery->where('sale_or_rent', '!=', 'Not Available')
                                    ->where('availability_status', 'Available')
                                    ->where('status', 'Approved');
                            })
                            ->with(['units' => function ($unitQuery) {
                                $unitQuery->where('sale_or_rent', '!=', 'Not Available')
                                    ->where('availability_status', 'Available')
                                    ->where('status', 'Approved')
                                    ->select('id', 'level_id', 'unit_name', 'unit_type', 'price', 'area', 'sale_or_rent', 'availability_status')
                                    ->with('pictures:unit_id,file_path');
                            }]);
                    }
                ])
                ->first();

            if (!$building) {
                return response()->json(['error' => 'Building not found'], 404);
            }

            return response()->json(['buildingUnits' => $building]);
        } catch (\Exception $e) {
            Log::error("Error in specificBuildingUnits: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching specific Building Units.'], 500);
        }
    }
}
