<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (backpack_auth()->guest()) {
            return redirect('/login');
        }

        if (! backpack_auth()->user()->hasRole($role)) {
            abort(403);
        }

        return $next($request);
    }
}
