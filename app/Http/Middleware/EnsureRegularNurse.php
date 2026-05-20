<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRegularNurse
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->user()?->isRegularNurse(), 403);

        return $next($request);
    }
}
