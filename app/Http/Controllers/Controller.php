<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\ManagerBuilding;
use Illuminate\Http\Request;

abstract class Controller
{
    protected function handleResponse(Request $request, $statusCode, $heading, $data, $redirectTo = null, $route = false)
    {
        if ($request->wantsJson()) {
            return response()->json([
                $heading => $data,
            ], $statusCode);
        }

        if ($redirectTo && $route) {
            return redirect()->route($redirectTo)->with($heading, $data);
        }
        if ($redirectTo) {
            return view($redirectTo, [$heading => $data]);
        }
        return redirect()->back()->with($heading, $data);
    }
}
