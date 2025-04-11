<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BillingCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class CheckOutController extends Controller
{
    public function index(Request $request){
        try {
            $selectedPackage = $request->input('package');
            $selectedCycle = $request->input('cycle');
            $planCycles = BillingCycle::pluck('duration_months');
        } catch (\Exception $e) {
            Log::error('Error fetching billing cycles: ' . $e->getMessage());
            $planCycles = collect();
        }

        return view('landing-views.checkout', compact('planCycles', 'selectedPackage', 'selectedCycle'));
    }


}
