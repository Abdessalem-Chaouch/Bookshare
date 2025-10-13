<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (! $request->user()) {
            abort(403);
        }

        if (! in_array($request->user()->role, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}

