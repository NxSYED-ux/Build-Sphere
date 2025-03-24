<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BuildingUnitsController extends Controller
{
    public function specificBuildingUnits(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json(['error' => 'Invalid building ID'], 400);
            }

            $building = Building::where('id', $id)
                ->select('id', 'name', 'address_id')
                ->with([
                    'address:id,location,city,province,country',
                    'levels:id,building_id,level_name',
                    'levels.units' => function ($query) {
                        $query->where('sale_or_rent', '!=', 'Not Available')
                            ->where('availability_status', 'Available')
                            ->where('status', 'Approved')
                            ->select('id', 'level_id', 'unit_name', 'unit_type', 'price', 'area', 'sale_or_rent', 'availability_status')
                            ->with('pictures:unit_id,file_path');
                    }
                ])
                ->first();

            if (!$building) {
                return response()->json(['error' => 'Building not found'], 404);
            }

            return response()->json(['buildingUnits' => $building], 200);
        } catch (\Exception $e) {
            Log::error("Error in specificBuildingUnits: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching specific Building Units.'], 500);
        }
    }
}
