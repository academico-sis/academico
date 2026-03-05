<?php

namespace App\Filament\Pages;

use App\Models\Partner;
use BackedEnum;
use Filament\Pages\Page;

class PartnershipsReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 840;

    protected string $view = 'filament.pages.partnerships-report';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('reports.view') ?? false;
    }

    /** @var array<int, array<string, mixed>> */
    public array $partnersData = [];

    public function mount(): void
    {
        $this->partnersData = Partner::orderBy('name')
            ->withCount('courses')
            ->get()
            ->map(fn ($partner) => [
                'id' => $partner->id,
                'name' => $partner->name,
                'started_on' => $partner->started_on?->format('Y-m-d') ?? '-',
                'expired_on' => $partner->expired_on?->format('Y-m-d') ?? '-',
                'auto_renewal' => $partner->auto_renewal,
                'courses_count' => $partner->courses_count,
            ])->toArray();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('Partnerships');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Partnerships Report');
    }
}
