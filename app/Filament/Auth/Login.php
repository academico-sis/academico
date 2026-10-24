<?php

namespace App\Filament\Auth;

use App\Models\User;
use App\Services\Settings;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
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

    /**
     * Fills the form with an account of the demo_accounts setting, which a demo install lists on this page.
     */
    public function useDemoAccount(int $index): void
    {
        $account = (app(Settings::class)->get('demo_accounts') ?? [])[$index] ?? null;

        if (! is_array($account)) {
            return;
        }

        $this->form->fill([
            'email' => $account['email'] ?? null,
            'password' => $account['password'] ?? null,
            'remember' => false,
        ]);
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
}
