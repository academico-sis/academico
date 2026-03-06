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
    </div>

    @if($selectedCourseId && count($gradeTypes) > 0 && count($enrollments) > 0)
        <x-filament::section>
            <x-slot name="heading">{{ __('Grades') }}</x-slot>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 sticky left-0 bg-gray-50 dark:bg-gray-700 z-10">{{ __('Student') }}</th>
                            @foreach($gradeTypes as $gt)
                                <th class="px-4 py-2 text-center">
                                    @if($gt['categoryName'])
                                        <span class="text-xs text-gray-400 block">({{ $gt['categoryName'] }})</span>
                                    @endif
                                    {{ $gt['name'] }}
                                    <span class="text-xs text-gray-400">/{{ $gt['total'] }}</span>
                                </th>
                            @endforeach
                            <th class="px-4 py-2 text-center font-bold">{{ __('Total') }}</th>
                            <th class="px-4 py-2 text-center">{{ __('Result') }}</th>
                            <th class="px-4 py-2 text-center">{{ __('Comment') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enrollments as $enrollment)
                            <tr class="border-b dark:border-gray-600">
                                <td class="px-4 py-2 sticky left-0 bg-white dark:bg-gray-800 z-10 whitespace-nowrap font-medium">
                                    {{ $enrollment['studentName'] }}
                                </td>
                                @foreach($gradeTypes as $gt)
                                    <td class="px-4 py-2 text-center">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="{{ $gt['total'] }}"
                                            value="{{ $enrollment['grades'][$gt['id']] }}"
                                            wire:change="saveGrade({{ $enrollment['enrollmentId'] }}, {{ $gt['id'] }}, $event.target.value)"
                                            class="w-20 text-center rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                        >
                                    </td>
                                @endforeach
                                <td class="px-4 py-2 text-center font-bold">
                                    {{ $enrollment['total'] ?: '' }}
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <select
                                        wire:change="saveResult({{ $enrollment['enrollmentId'] }}, $event.target.value)"
                                        class="w-28 text-xs rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    >
                                        <option value="">—</option>
                                        @foreach($resultTypes as $rt)
                                            <option value="{{ $rt['id'] }}" @selected($enrollment['resultTypeId'] == $rt['id'])>
                                                {{ $rt['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <textarea
                                        rows="1"
                                        wire:change="saveComment({{ $enrollment['enrollmentId'] }}, $event.target.value)"
                                        class="w-32 text-xs rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    >{{ $comments[$enrollment['enrollmentId']] ?? '' }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @elseif($selectedCourseId && count($gradeTypes) === 0)
        <x-filament::section>
            <p class="text-sm text-gray-500">{{ __('No evaluation type or grade types configured for this course.') }}</p>
        </x-filament::section>
    @elseif($selectedCourseId && count($enrollments) === 0)
        <x-filament::section>
            <p class="text-sm text-gray-500">{{ __('No enrollments found for this course.') }}</p>
        </x-filament::section>
    @endif
</x-filament-panels::page>
