<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        if ($response && Filament::auth()->user()?->isStudent()) {
            return new class implements LoginResponse
            {
                public function toResponse($request)
                {
                    return redirect()->route('student.dashboard');
                }
            };
        }

        return $response;
    }

    /**
     * Students sign in on this page too: they are redirected to their dashboard
     * instead of the panel. The parent class keeps the timing-safe credential and
     * panel access checks.
     */
    protected function isUserAllowedToAccessPanel(Authenticatable $user): bool
    {
        return ($user instanceof User && $user->isStudent()) || parent::isUserAllowedToAccessPanel($user);
    }

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString(__('filament-panels::auth/pages/login.actions.register.before').' '.$this->registerAction->toHtml());
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->link()
            ->label(__('filament-panels::auth/pages/login.actions.register.label'))
            ->url(route('register'));
    }

    public function getView(): string
    {
        if (View::exists('filament.auth.login')) {
            return 'filament.auth.login';
        }

        return parent::getView();
    }

    public function getMaxContentWidth(): Width|string|null
    {
        if (View::exists('filament.auth.login')) {
            return Width::FiveExtraLarge;
        }

        return parent::getMaxContentWidth();
    }
}
