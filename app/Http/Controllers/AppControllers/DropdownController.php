<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\DropdownType;
use App\Models\DropdownValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DropdownController extends Controller
{
    public function getDropdownValuesByType(Request $request, $type)
    {
        try {
            if (!$type) {
                return response()->json(['error' => 'Type is required'], 400);
            }

            $data = DropdownType::where('type_name', $type)
                ->where('status', 1)
                ->with(['values' => function ($query) {
                    $query->where('status', 1)->select('value_name', 'dropdown_type_id');
                }])
                ->get();

            if ($data->isEmpty()) {
                return response()->json(['error' => "No values found against type $type"], 404);
            }

            $valuesOnly = $data->flatMap(function ($type) {
                return $type->values->pluck('value_name');
            });

            return response()->json(['values' => $valuesOnly], 200);
        } catch (\Exception $e) {
            Log::error("Error in getDropdownValuesByType: " . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function getDropdownValuesByValue(Request $request, $value)
    {
        try {
            if (!$value) {
                return response()->json(['error' => 'Value is required'], 400);
            }

            $data = DropdownValue::where('value_name', $value)
                ->where('status', 1)
                ->with(['childs' => function ($query) {
                    $query->where('status', 1);
                }])
                ->get();

            if ($data->isEmpty()) {
                return response()->json(['error' => "No values found against $value"], 404);
            }

            $valuesOnly = $data->flatMap(function ($type) {
                return $type->childs->pluck('value_name');
            });

            return response()->json(['values' => $valuesOnly], 200);
        } catch (\Exception $e) {
            Log::error("Error in getDropdownValuesByValue: " . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
