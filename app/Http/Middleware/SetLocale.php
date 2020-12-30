<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Date;
use Illuminate\Support\Facades\App;
use Unicodeveloper\Identify\Facades\IdentityFacade as Identify;

class SetLocale
{
    /**
     * This function checks if language to set is an allowed lang of config.
     *
     * @param string $locale
     **/
    private function setLocale($locale)
    {
        // Check if is allowed and set default locale if not
        if (! language()->allowed($locale)) {
            $locale = config('app.locale');
        }

        // Set app language
        App::setLocale($locale);

        // Set carbon language
        if (config('language.carbon')) {
            // Carbon uses only language code
            if (config('language.mode.code') == 'long') {
                $locale = explode('-', $locale)[0];
            }

            Carbon::setLocale($locale);
        }

        // Set date language
        if (config('language.date')) {
            // Date uses only language code
            if (config('language.mode.code') == 'long') {
                $locale = explode('-', $locale)[0];
            }

            Date::setLocale($locale);
        }
    }

    public function setDefaultLocale()
    {
        if (config('language.auto')) {
            $this->setLocale(Identify::lang()->getLanguage());
        } else {
            $this->setLocale(config('app.locale'));
        }
    }

    public function setUserLocale()
    {
        $user = backpack_auth()->user();

        if ($user->locale) {
            $this->setLocale($user->locale);
        } else {
            $this->setDefaultLocale();
        }
    }

    public function setSystemLocale($request)
    {
        if ($request->session()->has('locale')) {
            $this->setLocale(session('locale'));
        } else {
            $this->setDefaultLocale();
        }
    }

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
        if (backpack_auth()->check()) {
            $this->setUserLocale();
        } else {
            $this->setSystemLocale($request);
        }

        return $next($request);
    }
}
