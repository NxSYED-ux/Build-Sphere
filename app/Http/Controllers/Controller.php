<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    protected function handleResponse(Request $request, $statusCode, $heading, $data, $redirectTo = null)
    {
        if ($request->wantsJson()) {
            return response()->json([
                $heading => $data,
            ], $statusCode);
        }

        if ($redirectTo) {
            return view($redirectTo, [$heading => $data]);
        }
        return redirect()->back()->with($heading, $data);
    }
}
