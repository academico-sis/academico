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


        if ((!\Request::is('update')) && backpack_user()->isStudent() && backpack_user()->student->force_update == 1) {
            return redirect('/update');
        }

        if ((!\Request::is('update/2')) && backpack_user()->isStudent() && backpack_user()->student->force_update == 2) {
            return redirect('/update/2');
        }

/*         if (((!\Request::is('update/3') || !\Request::is('update/3')) && backpack_user()->isStudent() && backpack_user()->student->force_update == 3) {
            return redirect('/update/3');
        } */

        if ((!\Request::is('update/4')) && backpack_user()->isStudent() && backpack_user()->student->force_update == 4) {
            return redirect('/update/4');
        }

        if ((!\Request::is('update/5')) && backpack_user()->isStudent() && backpack_user()->student->force_update == 5) {
            return redirect('/update/5');
        }

/*         if ((!\Request::is('update/6')) && backpack_user()->isStudent() && backpack_user()->student->force_update == 6) {
            return redirect('/update/6');
        } */

    }
        return $next($request);
    }
}
