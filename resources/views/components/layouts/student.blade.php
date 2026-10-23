<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <nav class="border-b border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="/" class="flex items-center gap-2 text-xl font-semibold text-gray-800">
                    @if (config('academico.logo'))
                        <img src="{{ Storage::disk('public')->url(config('academico.logo')) }}" alt="" class="h-8 w-auto">
                    @endif
                    {{ config('app.name') }}
                </a>
                <div class="flex items-center gap-4">
                    @auth
                        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                        <a href="{{ route('student.account') }}" class="text-sm text-blue-600 hover:text-blue-800">{{ __('My Account') }}</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">{{ __('Logout') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800">{{ __('Login') }}</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ __('Register') }}</a>
                    @endauth
                    @livewire('locale-switcher')
                </div>
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
