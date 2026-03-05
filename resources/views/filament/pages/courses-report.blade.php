<x-filament-panels::page>
    <div class="mb-4">
        <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Period') }}</label>
        <select wire:model.live="selectedPeriodId" id="period" class="block w-full max-w-xs rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
            @foreach(\App\Models\Period::all() as $period)
                <option value="{{ $period->id }}">{{ $period->name }}</option>
            @endforeach
        </select>
    </div>

    @if(count($coursesData) > 0)
        <x-filament::section class="mb-6">
            <x-slot name="heading">{{ __('Enrollments per Course') }}</x-slot>
            <div
                wire:ignore
                x-data="{ chart: null }"
                x-init="
                    const loadChartJs = () => new Promise(resolve => {
                        if (window.Chart) { resolve(); return; }
                        const s = document.createElement('script');
                        s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js';
                        s.onload = resolve;
                        document.head.appendChild(s);
                    });

                    const render = (data) => {
                        if (chart) { chart.destroy(); }
                        const canvas = $el.querySelector('canvas');
                        if (!canvas || !data.labels) return;
                        chart = new Chart(canvas, {
                            type: 'bar',
                            data: data,
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                scales: { x: { beginAtZero: true } },
                            }
                        });
                    };

                    loadChartJs().then(() => render($wire.chartData));

                    $wire.$watch('chartData', (newData) => render(newData));
                "
            >
                <canvas height="100"></canvas>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">{{ __('Course Details') }}</x-slot>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2">{{ __('Course') }}</th>
                            <th class="px-4 py-2">{{ __('Rhythm') }}</th>
                            <th class="px-4 py-2">{{ __('Level') }}</th>
                            <th class="px-4 py-2">{{ __('Teacher') }}</th>
                            <th class="px-4 py-2 text-right">{{ __('Enrollments') }}</th>
                            <th class="px-4 py-2 text-right">{{ __('Volume (h)') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coursesData as $course)
                            <tr class="border-b dark:border-gray-600">
                                <td class="px-4 py-2 font-medium">{{ $course['name'] }}</td>
                                <td class="px-4 py-2">{{ $course['rhythmName'] }}</td>
                                <td class="px-4 py-2">{{ $course['levelName'] }}</td>
                                <td class="px-4 py-2">{{ $course['teacherName'] }}</td>
                                <td class="px-4 py-2 text-right">{{ $course['enrollments'] }}</td>
                                <td class="px-4 py-2 text-right">{{ $course['totalVolume'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    @if(count($coursesData) > 1)
                        <tfoot class="bg-gray-50 dark:bg-gray-700 font-semibold">
                            <tr>
                                <td class="px-4 py-2" colspan="4">{{ __('Average') }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format(collect($coursesData)->avg('enrollments'), 1) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format(collect($coursesData)->avg('totalVolume'), 1) }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2" colspan="4">{{ __('Total') }}</td>
                                <td class="px-4 py-2 text-right">{{ collect($coursesData)->sum('enrollments') }}</td>
                                <td class="px-4 py-2 text-right">{{ collect($coursesData)->sum('totalVolume') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </x-filament::section>
    @else
        <x-filament::section>
            <p class="text-sm text-gray-500">{{ __('No courses found for this period.') }}</p>
        </x-filament::section>
    @endif


</x-filament-panels::page>
