<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The student pages are only for students. Other users are sent to the panel.
 */
class EnsureUserIsStudent
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->student) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
