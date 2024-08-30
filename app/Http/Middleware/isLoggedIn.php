<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;

class isLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (backpack_auth()->guest()) {
            return redirect()->to('/login');
        }

        return $next($request);
    }
}
