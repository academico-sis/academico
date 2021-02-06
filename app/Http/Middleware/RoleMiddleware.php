<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
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
