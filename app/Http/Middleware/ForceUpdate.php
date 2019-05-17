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
        // if the current user has a forceupdate field set, we check that they can only access this route or a lower forceupdate step

        if (backpack_user() != null)
        {
            if(backpack_user()->isStudent())
            {
                if (backpack_user()->student->force_update)
                {
                    if (\Route::current() != 'update/' . backpack_user()->student->force_update)
                    {
                        return 'redirect';
                    }
                }
 
/*                 if (\Route::current()->getName() != 'backpack.account.info' && backpack_user()->student->force_update == 1) {
                    \Alert::warning(__('Please update your data before you continue your navigation'))->flash();
                    return redirect(route('backpack.account.info'));
                }

                if (\Route::current()->getName() != 'backpack.student.info' && backpack_user()->student->force_update == 2) {
                    \Alert::warning(__('Please update your data before you continue your navigation'))->flash();
                    return redirect(route('backpack.student.info'));
                }

                if (\Request::route()->getName() != 'backpack.account.phone' && backpack_user()->student->force_update == 3) {
                    return redirect(route('backpack.account.phone'));
                }

                if (\Request::route()->getName() != 'backpack.account.profession' && backpack_user()->student->force_update == 4) {
                    return redirect(route('backpack.account.profession'));
                }

                if (\Request::route()->getName() != 'backpack.account.photo' && backpack_user()->student->force_update == 5) {
                    return redirect(route('backpack.account.photo'));
                }

                if (\Request::route()->getName() != 'backpack.account.contacts' && backpack_user()->student->force_update == 6) {
                    return redirect(route('backpack.account.contacts'));
                } */
            }
        }

        return $next($request);
    }
}
