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
            return redirect(route('backpack.account.info'));
        }

        if (backpack_user()->isStudent() && backpack_user()->student->force_update == 2) {
            return redirect(route('backpack.student.info'));
        }

         if (backpack_user()->isStudent() && backpack_user()->student->force_update == 3) {
            return redirect(route('backpack.account.phone'));
        }

        if (backpack_user()->isStudent() && backpack_user()->student->force_update == 4) {
            return redirect(route('backpack.account.profession'));
        }

        if (backpack_user()->isStudent() && backpack_user()->student->force_update == 5) {
            return redirect(route('backpack.account.photo'));
        }

         if (backpack_user()->isStudent() && backpack_user()->student->force_update == 6) {
            return redirect(route('backpack.account.contacts'));
        }

    }
        return $next($request);
    }
}
