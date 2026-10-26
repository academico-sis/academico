{{-- Accounts of a demo install, stored in the demo_accounts setting by the demo seeder, with a warning for visitors --}}
@php($accounts = app(\App\Services\Settings::class)->get('demo_accounts') ?? [])
@php($mailboxUrl = config('services.demo_mailbox.url'))

@if ($accounts !== [])
    <x-filament::callout :heading="__('Public demo')" icon="heroicon-o-exclamation-triangle" color="warning">
        <x-slot name="description">
            {{-- The description is a paragraph: its lines are spans --}}
            <span class="block">{{ __('Anyone can see what you enter here. Do not enter real names, email addresses or other personal data.') }}</span>

            @if (filled($mailboxUrl))
                <span class="mt-2 block">
                    {{ __('Emails are not delivered: they all go to a public mailbox, where you can read them, for example after ":action".', ['action' => __('filament-panels::auth/pages/login.actions.request_password_reset.label')]) }}
                    <x-filament::link :href="$mailboxUrl" target="_blank" rel="noopener">{{ __('Open the demo mailbox') }}</x-filament::link>
                </span>
            @endif
        </x-slot>
    </x-filament::callout>

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
