<?php

namespace App\Http\Middleware;

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
