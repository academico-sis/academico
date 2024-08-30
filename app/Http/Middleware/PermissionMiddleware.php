<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
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
