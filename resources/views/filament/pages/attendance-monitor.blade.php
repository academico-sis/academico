<x-filament-panels::page>
    <div class="mb-4 flex flex-wrap items-end gap-4">
        <div>
            <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Period') }}</label>
            <select wire:model.live="selectedPeriodId" id="period" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                @foreach(\App\Models\Period::all() as $period)
                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Search') }}</label>
            <input type="text" wire:model.live.debounce.300ms="search" id="search" placeholder="{{ __('Course name...') }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
        </div>
        <div>
            <label for="teacher" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Teacher') }}</label>
            <select wire:model.live="selectedTeacherId" id="teacher" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                <option value="">{{ __('All Teachers') }}</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="dateFrom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('From') }}</label>
            <input type="date" wire:model.live="dateFrom" id="dateFrom" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
        </div>
        <div>
            <label for="dateTo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('To') }}</label>
            <input type="date" wire:model.live="dateTo" id="dateTo" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Absences per Student --}}
        <x-filament::section>
            <x-slot name="heading">{{ __('Absences per Student') }}</x-slot>

            @if(count($absencesPerStudent) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2">{{ __('Student') }}</th>
                                <th class="px-4 py-2">{{ __('Course') }}</th>
                                <th class="px-4 py-2 text-right">{{ __('Absences') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($absencesPerStudent as $record)
                                <tr class="border-b dark:border-gray-600">
                                    <td class="px-4 py-2">
                                        <a href="{{ route('filament.admin.pages.student-attendance', ['studentId' => $record['studentId'], 'courseId' => $record['courseId']]) }}" class="text-primary-600 hover:underline">
                                            {{ $record['studentName'] }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2">{{ $record['courseName'] }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <x-filament::badge color="danger">{{ $record['absencesCount'] }}</x-filament::badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">{{ __('No absences recorded for this period.') }}</p>
            @endif
        </x-filament::section>

        {{-- Courses with Missing Attendance --}}
        <x-filament::section>
            <x-slot name="heading">{{ __('Courses with Missing Attendance') }}</x-slot>

            @if(count($coursesData) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2">{{ __('Course') }}</th>
                                <th class="px-4 py-2">{{ __('Teacher') }}</th>
                                <th class="px-4 py-2 text-right">{{ __('Missing Events') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coursesData as $course)
                                <tr class="border-b dark:border-gray-600">
                                    <td class="px-4 py-2">
                                        <a href="{{ route('filament.admin.pages.course-attendance', ['courseId' => $course['id']]) }}" class="text-primary-600 hover:underline">
                                            {{ $course['name'] }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2">{{ $course['teacherName'] }}</td>
                                    <td class="px-4 py-2 text-right">
                                        @if($course['missing'] > 0)
                                            <x-filament::badge color="warning">{{ $course['missing'] }}</x-filament::badge>
                                        @else
                                            <x-filament::badge color="success">{{ __('OK') }}</x-filament::badge>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">{{ __('No courses with events and enrollments found.') }}</p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
