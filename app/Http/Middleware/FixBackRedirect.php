<?php

namespace App\Http\Middleware;

use Closure;

class FixBackRedirect
{
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('get') && !$request->ajax()) {
            $request->session()->put('_previous.url', $request->fullUrl());
        }

        return $next($request);
    }
}
