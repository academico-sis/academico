<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
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
