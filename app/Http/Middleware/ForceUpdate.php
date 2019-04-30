<?php

namespace App\Http\Middleware;

use Closure;

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
        
if (backpack_user() != null) {

        if (backpack_user()->isStudent() && backpack_user()->student->force_update == 1) {
            return redirect('/update');
        }

        if (backpack_user()->isStudent() && backpack_user()->student->force_update == 2) {
            return redirect('/update/2');
        }

         if (backpack_user()->isStudent() && backpack_user()->student->force_update == 3) {
            return redirect('/update/3');
        }

        if (backpack_user()->isStudent() && backpack_user()->student->force_update == 4) {
            return redirect('/update/4');
        }

        if (backpack_user()->isStudent() && backpack_user()->student->force_update == 5) {
            return redirect('/update/5');
        }

         if (backpack_user()->isStudent() && backpack_user()->student->force_update == 6) {
            return redirect('/update/6');
        }

    }
        return $next($request);
    }
}
