<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Period selector --}}
        <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Period') }}</label>
            <select wire:model.live="selectedPeriodId" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                @foreach ($periods as $period)
                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Volume summary --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-filament::section>
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('In-person hours') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($volume, 1) }}</p>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Remote hours') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($remoteVolume, 1) }}</p>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total hours') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalVolume, 1) }}</p>
                </div>
            </x-filament::section>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Courses --}}
            <div class="lg:col-span-2">
                <x-filament::section :heading="__('Results') . ' (' . count($results) . ')'" class="mb-6" collapsible>
                    @if (empty($results))
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No results for this period.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2">{{ __('Student') }}</th>
                                        <th class="px-4 py-2">{{ __('Course') }}</th>
                                        <th class="px-4 py-2">{{ __('Result') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $result)
                                        <tr class="border-b dark:border-gray-600">
                                            <td class="px-4 py-2">{{ $result['studentName'] }}</td>
                                            <td class="px-4 py-2">{{ $result['courseName'] }}</td>
                                            <td class="px-4 py-2">
                                                <x-filament::badge>{{ $result['resultName'] }}</x-filament::badge>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </x-filament::section>

                <x-filament::section :heading="__('My Courses') . ' (' . count($courses) . ')'">
                    @if (empty($courses))
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No courses for this period.') }}</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($courses as $course)
                                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $course['name'] }}</h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $course['start_date'] }} — {{ $course['end_date'] }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $course['enrollments_count'] }} {{ __('students') }}
                                            </span>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $course['room'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-filament::section>
            </div>

            {{-- Pending attendance --}}
            <div>
                <x-filament::section :heading="__('Pending Attendance')">
                    @if (empty($pendingAttendance))
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('All attendance is up to date.') }}</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($pendingAttendance as $event)
                                <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-900/20">
                                    <p class="text-sm font-medium text-amber-800 dark:text-amber-200">{{ $event['name'] }}</p>
                                    <p class="text-xs text-amber-600 dark:text-amber-400">{{ $event['course_name'] }}</p>
                                    <p class="text-xs text-amber-600 dark:text-amber-400">{{ $event['start'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-filament::section>
            </div>
        </div>
    </div>
</x-filament-panels::page>
