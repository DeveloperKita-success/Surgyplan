<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureDoctor
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->user()?->isDoctor(), 403);

        return $next($request);
    }
}
