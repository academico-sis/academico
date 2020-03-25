<?php

namespace App\Http\Middleware;

use Closure;

class isLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (backpack_auth()->guest()) {
            return redirect()->to('/login');
        }

        return $next($request);
    }
}
