{{-- Accounts of a demo install, stored in the demo_accounts setting by the demo seeder --}}
@php($accounts = app(\App\Services\Settings::class)->get('demo_accounts') ?? [])

@if ($accounts !== [])
    <x-filament::section :heading="__('Demo accounts')" compact>
        <div class="divide-y divide-gray-200 text-sm dark:divide-white/10">
            @foreach ($accounts as $index => $account)
                <div class="flex items-center justify-between gap-3 py-2">
                    <div class="min-w-0">
                        <p class="font-medium">{{ __($account['role'] ?? '') }}</p>
                        <p class="truncate text-gray-500 dark:text-gray-400">{{ $account['email'] ?? '' }} · {{ $account['password'] ?? '' }}</p>
                    </div>
                    <x-filament::button size="xs" color="gray" wire:click="useDemoAccount({{ $index }})">
                        {{ __('Use') }}
                    </x-filament::button>
                </div>
            @endforeach
        </div>
    </x-filament::section>
@endif
