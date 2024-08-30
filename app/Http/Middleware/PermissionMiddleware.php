<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (backpack_auth()->guest()) {
            return redirect()->to('login');
        }

        if (! backpack_user()->can($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
