<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUkNurse
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->user()?->isUkNurse(), 403);

        return $next($request);
    }
}
