<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\BuildingUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CheckOutController extends Controller
{
    public function unitsOnlinePayment(Request $request){
        $request->validate([
             'payment_method_id' => 'required|string',
             'unit_id' => 'required|integer',
        ]);

        $user = request()->user() ?? null;

        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        DB::beginTransaction();

        $unit = BuildingUnit::where('id', $request->unit_id)
            ->where('availability_status', 'Available')
            ->where('sale_or_rent', '!=', 'Not Available')
            ->sharedLock()->first();
    }

    public function membershipsOnlinePayment(Request $request){

    }

}
