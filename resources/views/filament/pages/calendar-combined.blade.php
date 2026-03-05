<x-filament-panels::page>
    <div class="flex flex-wrap items-end gap-4 mb-6">
        <div class="min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Teachers') }}</label>
            <select wire:model.live="selectedTeacherIds" multiple
                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                style="min-height: 80px;">
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher['id'] }}">{{ $teacher['name'] }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Hold Ctrl/Cmd to select multiple') }}</p>
        </div>

        <div class="min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Rooms') }}</label>
            <select wire:model.live="selectedRoomIds" multiple
                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                style="min-height: 80px;">
                @foreach($rooms as $room)
                    <option value="{{ $room['id'] }}">{{ $room['name'] }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Hold Ctrl/Cmd to select multiple') }}</p>
        </div>
    </div>

    @if(count($selectedTeacherIds) > 0)
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($teachers as $teacher)
                @if(in_array($teacher['id'], $selectedTeacherIds))
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium text-white"
                        style="background-color: {{ $teacherColors[$teacher['id']] ?? '#3b82f6' }}">
                        {{ $teacher['name'] }}
                    </span>
                @endif
            @endforeach
        </div>
    @endif

    <x-filament::section>
        <div
            wire:ignore
            x-data="{ events: @js($events) }"
            x-init="
                const loadScript = (src) => new Promise((resolve) => {
                    if (document.querySelector(`script[src='${src}']`)) { resolve(); return; }
                    const s = document.createElement('script');
                    s.src = src;
                    s.onload = resolve;
                    document.head.appendChild(s);
                });

                const loadStyle = (href) => {
                    if (document.querySelector(`link[href='${href}']`)) return;
                    const l = document.createElement('link');
                    l.rel = 'stylesheet';
                    l.href = href;
                    document.head.appendChild(l);
                };

                loadStyle('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css');

                const initFn = () => {
                    loadScript('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js').then(() => {
                        const mapEvents = (evts) => evts.map(e => ({
                            id: e.id, title: e.title, start: e.start, end: e.end,
                            backgroundColor: e.color, borderColor: e.color,
                            extendedProps: { teacher: e.teacher, room: e.room }
                        }));

                        const calendar = new FullCalendar.Calendar($el, {
                            initialView: 'timeGridWeek',
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                            },
                            events: mapEvents(events),
                            eventDidMount: function(info) {
                                const teacher = info.event.extendedProps.teacher;
                                const room = info.event.extendedProps.room;
                                let tooltip = info.event.title;
                                if (teacher) tooltip += '\n{{ __('Teacher') }}: ' + teacher;
                                if (room) tooltip += '\n{{ __('Room') }}: ' + room;
                                info.el.title = tooltip;
                            },
                            slotMinTime: '07:00:00',
                            slotMaxTime: '22:00:00',
                            allDaySlot: false,
                            locale: '{{ app()->getLocale() }}',
                            height: 'auto',
                        });
                        calendar.render();

                        Livewire.on('eventsUpdated', ({ events }) => {
                            calendar.removeAllEvents();
                            calendar.addEventSource(mapEvents(events));
                        });
                    });
                };

                initFn();
            "
        ></div>
    </x-filament::section>
</x-filament-panels::page>
