<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BillingCycle;
use Illuminate\Support\Facades\Log;


class landingController extends Controller
{
    public function index()
    {
        try {
            $planCycles = BillingCycle::pluck('duration_months');
        } catch (\Throwable $e) {
            Log::error('Error fetching billing cycles: ' . $e->getMessage());
            $planCycles = collect();
        }

        $selectedPlanCycle = $planCycles->first();

        return view('landing-views.index', compact('planCycles', 'selectedPlanCycle'));
    }

    public function aboutUs()
    {
        return view('landing-views.aboutUs');
    }

    public function contactUs()
    {
        return view('landing-views.contactUs');
    }

}
