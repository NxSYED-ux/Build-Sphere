<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\UserBuildingUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MyPropertiesController extends Controller
{
    public function showMyProperties(Request $request)
    {
        try {
            $user = $request->user() ?? null;
            if (!$user) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }

            $userUnits = UserBuildingUnit::where('user_id', $user->id)
                ->where('contract_status', 1)
                ->select('id', 'unit_id', 'type', 'price', 'rent_start_date', 'rent_end_date', 'purchase_date')
                ->with([
                    'unit:id,level_id,unit_name,area',
                    'unit.level:id,building_id,level_name',
                    'unit.level.building:id,name,address_id',
                    'unit.level.building.address:id,location,city,province,country',
                    'unit.pictures:unit_id,file_path'
                ])
                ->whereHas('unit', function ($query) {
                    $query->where('status', 'Approved');
                })
                ->orderBy('updated_at', 'DESC')
                ->get();

            return response()->json(['myProperties' => $userUnits], 200);
        } catch (\Exception $e) {
            Log::error("Error in showMyProperties: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching My Properties data.'], 500);
        }
    }

    public function myPropertyDetails(Request $request, $id)
    {
        try {
            $user = $request->user() ?? null;
            if (!$user) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }

            if (!is_numeric($id)) {
                return response()->json(['error' => 'Invalid unit ID'], 400);
            }

            $myUnitDetails = UserBuildingUnit::where('id', $id)
                ->where('user_id', $user->id)
                ->select('id', 'unit_id', 'type', 'price', 'rent_start_date', 'rent_end_date', 'purchase_date')
                ->with([
                    'unit:id,level_id,unit_name',
                    'unit.level:id,building_id,level_name',
                    'unit.level.building:id,name,address_id',
                    'unit.level.building.address:id,location,city,province,country',
                    'unit.pictures:unit_id,file_path',
                    'unit.queries' => function ($q) use ($user) {
                        $q->select('id', 'description', 'status', 'expected_closure_date', 'remarks', 'created_at', 'unit_id')
                            ->where('user_id', $user->id);
                    }
                ])
                ->first();

            return response()->json(['Details' => $myUnitDetails], 200);
        } catch (\Exception $e) {
            Log::error("Error in myPropertyDetails: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching details page data.'], 500);
        }
    }

}
