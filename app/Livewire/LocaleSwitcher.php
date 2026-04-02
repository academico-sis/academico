<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Livewire\Component;

class LocaleSwitcher extends Component
{
    public string $currentLocale;

    public function mount(): void
    {
        $this->currentLocale = App::getLocale();
    }

    public function switchLocale(string $locale): void
    {
        $allowed = config('app.translatable_locales', ['en', 'es', 'fr']);

        if (! in_array($locale, $allowed)) {
            return;
        }

        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        } else {
            session()->put('locale', $locale);
        }

        $this->currentLocale = $locale;
        App::setLocale($locale);

        $referer = request()->header('Referer', '/');
        $parsed = parse_url($referer);
        $path = ($parsed['path'] ?? '/') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
        $this->redirect($path, navigate: true);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.locale-switcher', [
            'locales' => config('app.translatable_locales', ['en', 'es', 'fr']),
        ]);
    }
}
