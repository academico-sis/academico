<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;

class ForceUpdate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Request::isMethod('post')) {
            return $next($request);
        }

        // if the current user has a forceupdate field set, we check that they can only access this route or a lower forceupdate step

        if (backpack_user() != null) {
            if (backpack_user()->isStudent()) {
                if (backpack_user()->student->force_update) {
                    // if the user wants to go further than authorized, redirect them to the current step
                    if ($request->path() != 'edit/'.backpack_user()->student->force_update) {
                        return redirect(url('edit/'.backpack_user()->student->force_update));
                    }
                }
            }
        }

        return $next($request);
    }
}
