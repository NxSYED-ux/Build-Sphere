<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\BuildingUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnitDetailsController extends Controller
{
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
                ->with([
                    'level:id,building_id,level_name',
                    'level.building:id,name,address_id',
                    'level.building.address:id,location,city,province,country',
                    'pictures:unit_id,file_path',
                    'organization:id,name',
                    'organization.pictures:organization_id,file_path'
                ])
                ->first();

            if (!$unitDetails) {
                return response()->json(['error' => 'Unit not found'], 404);
            }

            return response()->json([
                'unitDetails' => $unitDetails,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error in unitDetails: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching details page data.'], 500);
        }
    }
}
