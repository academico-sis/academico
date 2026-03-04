<x-filament-panels::page>
    @if ($this->viewMode === 'list')
        {{ $this->table }}
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
            @forelse ($this->getRosterEnrollments() as $enrollment)
                <a
                    href="{{ \App\Filament\Resources\Enrollments\EnrollmentResource::getUrl('view', ['record' => $enrollment]) }}"
                    class="group block overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 transition hover:shadow-md dark:bg-gray-900 dark:ring-white/10"
                >
                    <div class="aspect-square overflow-hidden bg-gray-100 dark:bg-gray-800">
                        @if ($enrollment->student->image)
                            <img
                                src="{{ $enrollment->student->image }}"
                                alt="{{ $enrollment->student->name }}"
                                class="h-full w-full object-cover transition group-hover:scale-105"
                            />
                        @else
                            <div class="flex h-full w-full items-center justify-center text-gray-400 dark:text-gray-500">
                                <x-heroicon-o-user-circle class="h-20 w-20" />
                            </div>
                        @endif
                    </div>
                    <div class="p-3 text-center">
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $enrollment->student->name }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('No enrollments found.') }}
                </div>
            @endforelse
        </div>
    @endif
</x-filament-panels::page>
