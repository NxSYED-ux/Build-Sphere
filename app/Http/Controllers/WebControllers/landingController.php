<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanServicePrice;

class landingController extends Controller
{
    public function index(){
        $planCycles = PlanServicePrice::select('billing_cycle')
            ->distinct()
            ->pluck('billing_cycle');

        return view('landing-views.index', compact('planCycles'));
    }

    public function checkout(){
        return view('landing-views.checkout');
    }

    public function plans()
    {
        $plans = Plan::where('status', 1)
            ->with(['services' => function ($query) {
                $query->where('status', 1)
                    ->with('prices');
            }])
            ->get();

        return response()->json(['plans' => $plans]);
    }

}
