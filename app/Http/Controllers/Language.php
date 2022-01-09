<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Language extends Controller
{
    /**
     * Set locale if it's allowed.
     *
     * @param string                   $locale
     * @param Request $request
     **/
    private function setLocale($locale, $request)
    {

        // Check if is allowed and set default locale if not
        if (! language()->allowed($locale)) {
            $locale = config('app.locale');
        }

        if (backpack_auth()->check()) {
            backpack_auth()->user()->setAttribute('locale', $locale)->save();
        } else {
            $request->session()->put('locale', $locale);
        }
    }

    /**
     * Set locale and return home url.
     *
     * @param string                   $locale
     *
     * @return string
     **/
    public function home($locale, Request $request)
    {
        $this->setLocale($locale, $request);

        $url = config('language.url') ? url('/'.$locale) : url('/');

        return redirect()->to($url);
    }

    /**
     * Set locale and return back.
     *
     * @param string                   $locale
     *
     * @return string
     **/
    public function back($locale, Request $request)
    {
        $this->setLocale($locale, $request);

        $session = $request->session();

        if (config('language.url')) {
            $previous_url = substr(str_replace(config('settings.app_url'), '', $session->previousUrl()), 7);

            if (strlen($previous_url) == 3) {
                $previous_url = substr($previous_url, 3);
            } else {
                $previous_url = substr($previous_url, strrpos($previous_url, '/') + 1);
            }

            $url = rtrim(config('settings.app_url'), '/').'/'.$locale.'/'.ltrim($previous_url, '/');

            $session->setPreviousUrl($url);
        }

        return redirect()->to($session->previousUrl());
    }
}
