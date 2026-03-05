<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Period;
use App\Services\StatService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $period = Period::get_default_period();

        if (! $period) {
            return [];
        }

        $stats = new StatService(external: false, partner: null, reference: $period);

        return [
            Stat::make(__('Enrollments'), $stats->enrollmentsCount())
                ->description($period->name)
                ->icon('heroicon-o-academic-cap')
                ->color('primary'),

            Stat::make(__('Paid Enrollments'), $stats->paidEnrollmentsCount())
                ->description(__('Pending').': '.$stats->pendingEnrollmentsCount())
                ->icon('heroicon-o-credit-card')
                ->color('success'),

            Stat::make(__('Students'), $stats->studentsCount())
                ->description($period->name)
                ->icon('heroicon-o-user-group')
                ->color('info'),

            Stat::make(__('New Students'), $stats->newStudentsCount())
                ->description($period->name)
                ->icon('heroicon-o-user-plus')
                ->color('warning')
                ->url(StudentResource::getUrl('index', ['tableFilters' => ['new_in_period' => ['isActive' => true]]])),
        ];
    }
}
