<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return response('Unauthoried.', 401);
        }

        if ($request->user()->role !== $role) {
            return response('Unauthoried.', 401);
        }

        return $next($request);
    }
}
