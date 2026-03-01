<x-filament-panels::page>
    <div class="flex flex-wrap items-end gap-4 mb-6">
        <div>
            <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Period') }}</label>
            <select wire:model.live="selectedPeriodId" id="period" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                @foreach(\App\Models\Period::all() as $period)
                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="course" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Course') }}</label>
            <select wire:model.live="selectedCourseId" id="course" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                <option value="">{{ __('Select a course...') }}</option>
                @foreach($courses as $course)
                    <option value="{{ $course['id'] }}">{{ $course['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="enrollment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Student') }}</label>
            <select wire:model.live="selectedEnrollmentId" id="enrollment" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                <option value="">{{ __('Select a student...') }}</option>
                @foreach($enrollments as $enrollment)
                    <option value="{{ $enrollment['id'] }}">{{ $enrollment['studentName'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($selectedEnrollmentId && count($skills) > 0)
        <x-filament::section>
            <x-slot name="heading">{{ __('Skills') }}</x-slot>

            <div class="space-y-4">
                @foreach($skills as $skill)
                    <div class="flex flex-col gap-2 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $skill['name'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $skill['typeName'] }} · {{ $skill['levelName'] }}
                            </p>
                        </div>
                        <div class="flex gap-1">
                            @foreach($scales as $scale)
                                @php
                                    $isActive = ($evaluations[$skill['id']] ?? null) === $scale['id'];
                                    $scaleColor = $isActive ? ($scale['color'] ?? 'primary') : 'gray';
                                @endphp
                                <x-filament::button
                                    wire:click="setEvaluation({{ $skill['id'] }}, {{ $scale['id'] }})"
                                    :color="$scaleColor"
                                    :outlined="!$isActive"
                                    size="xs"
                                >
                                    {{ $scale['shortname'] ?? $scale['name'] }}
                                </x-filament::button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @elseif($selectedEnrollmentId && count($skills) === 0)
        <x-filament::section>
            <p class="text-sm text-gray-500">{{ __('No skills configured for this course.') }}</p>
        </x-filament::section>
    @endif
</x-filament-panels::page>
