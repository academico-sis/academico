<x-filament-panels::page>
    <div class="flex flex-col gap-6 lg:flex-row">
        {{-- Sidebar filters --}}
        <div class="w-full shrink-0 space-y-4 lg:w-64">
            <x-filament::section :heading="__('Period')">
                <select wire:model.live="selectedPeriodId" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}">{{ $period->name }}</option>
                    @endforeach
                </select>
            </x-filament::section>

            <x-filament::section :heading="__('Teacher')">
                <select wire:model.live="selectedTeacherId" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    <option value="">{{ __('All teachers') }}</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </x-filament::section>

            @if ($rhythms->isNotEmpty())
                <x-filament::section :heading="__('Rhythm')">
                    <div class="space-y-2">
                        @foreach ($rhythms as $rhythm)
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="checkbox" wire:model.live="selectedRhythmIds" value="{{ $rhythm->id }}" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                {{ $rhythm->name }}
                            </label>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif

            @if ($levels->isNotEmpty())
                <x-filament::section :heading="__('Level')">
                    <div class="space-y-2">
                        @foreach ($levels as $level)
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="checkbox" wire:model.live="selectedLevelIds" value="{{ $level->id }}" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                {{ $level->name }}
                            </label>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif
        </div>

        {{-- Course cards grid --}}
        <div class="flex-1">
            @if (empty($courses))
                <div class="rounded-lg border border-gray-200 p-8 text-center dark:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No courses found for this period.') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($courses as $course)
                        @php
                            $borderClass = 'border-gray-200 dark:border-gray-700';
                            if ($course['enrollments_count'] === 0) {
                                $borderClass = 'border-red-400 dark:border-red-600';
                            } elseif (! $course['has_teacher'] || ! $course['has_room']) {
                                $borderClass = 'border-amber-400 dark:border-amber-600';
                            }
                        @endphp
                        <div class="relative rounded-lg border-2 {{ $borderClass }} bg-white shadow-sm transition hover:shadow-md dark:bg-gray-800">
                            @if ($course['edit_url'])
                                {{-- Actions dropdown --}}
                                <div x-data="{ open: false }" class="absolute right-2 top-2 z-10">
                                    <button @click.prevent="open = !open" type="button" class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                                        <x-heroicon-m-ellipsis-vertical class="h-5 w-5" />
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-transition
                                        class="absolute right-0 mt-1 w-40 rounded-md border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-600 dark:bg-gray-700">
                                        <a href="{{ $course['edit_url'] }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600">
                                            <x-heroicon-m-pencil-square class="h-4 w-4" />
                                            {{ __('Edit') }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            {{-- Card content - links to enrollments --}}
                            <a href="{{ $course['enrollments_url'] }}" class="block p-4">
                                <div class="mb-2 flex items-start justify-between gap-2 pr-6">
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $course['name'] }}</h3>
                                    <span class="inline-flex shrink-0 items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $course['enrollments_count'] > 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $course['enrollments_count'] }} {{ __('students') }}
                                    </span>
                                </div>

                                <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <p>
                                        <span class="font-medium">{{ __('Teacher') }}:</span>
                                        {{ $course['teacher'] }}
                                    </p>

                                    <p>
                                        <span class="font-medium">{{ __('Room') }}:</span>
                                        {{ $course['room'] ?? '-' }}
                                    </p>

                                    @if ($course['schedule'])
                                        <p>
                                            <span class="font-medium">{{ __('Schedule') }}:</span>
                                            {{ $course['schedule'] }}
                                        </p>
                                    @endif

                                    <p>
                                        {{ $course['start_date'] }} — {{ $course['end_date'] }}
                                    </p>

                                    <div class="flex items-center justify-between pt-1">
                                        @if ($course['volume'])
                                            <span>{{ $course['volume'] }}h</span>
                                        @endif

                                        @if ($course['spots'])
                                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ $course['spots'] - $course['enrollments_count'] }} {{ __('spots left') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
