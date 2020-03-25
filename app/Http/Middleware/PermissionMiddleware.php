<?php

namespace App\Http\Middleware;

use Closure;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if (backpack_auth()->guest()) {
            return redirect()->to('login');
        }

        if (!backpack_user()->can($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
