<?php

namespace App\Filament\Resources\Results;

use App\Filament\Exports\ResultExporter;
use App\Filament\Resources\Results\Pages\ManageResults;
use App\Filament\Resources\Results\Pages\ViewResult;
use App\Interfaces\CertificatesInterface;
use App\Models\Enrollment;
use App\Models\Period;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Read-only resource to monitor enrollment results.
 * Results are assigned through the grade editing and skill evaluation pages.
 */
class ResultResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?int $navigationSort = 6;

    protected static ?string $slug = 'results';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('evaluation.view') ?? false;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'A revoir / WIP';
    }

    public static function getModelLabel(): string
    {
        return __('Result');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Results');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['student.user', 'course.period', 'result.result_name']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Mobile: stacked student + course
                TextColumn::make('mobile_student')
                    ->label(__('Student'))
                    ->state(fn ($record) => $record->student?->name)
                    ->description(fn ($record) => $record->course?->name)
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereHas('student', fn ($q) => $q->whereHas('user', fn ($q) => $q->where('firstname', 'like', "%{$search}%")->orWhere('lastname', 'like', "%{$search}%"))))
                    ->wrap()
                    ->hiddenFrom('md'),
                // Desktop columns
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->wrap()
                    ->width('160px')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('student', function ($q) use ($search) {
                            $q->whereHas('user', function ($q) use ($search) {
                                $q->where('firstname', 'like', "%{$search}%")
                                    ->orWhere('lastname', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                        });
                    })
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('course.name')
                    ->label(__('Course'))
                    ->wrap()
                    ->width('180px')
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('course.period.name')
                    ->label(__('Period'))
                    ->sortable()
                    ->visibleFrom('lg'),
                TextColumn::make('result.result_type')
                    ->label(__('Result'))
                    ->sortable()
                    ->badge()
                    ->color(fn (Enrollment $record): ?array => $record->result?->result_name?->color ? Color::hex($record->result->result_name->color) : null),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                TernaryFilter::make('has_result')
                    ->label(__('No Result'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('result'),
                        false: fn (Builder $query) => $query->doesntHave('result'),
                    ),
                TernaryFilter::make('hide_parents')
                    ->label(__('Hide Parents'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereDoesntHave('childrenEnrollments')
                            ->whereIn('status_id', ['1', '2']),
                        false: fn (Builder $query) => $query,
                    ),
                SelectFilter::make('period_id')
                    ->label(__('Period'))
                    ->options(fn () => Period::withoutGlobalScopes()->orderByDesc('id')->pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data): Builder => $data['value']
                        ? $query->whereHas('course', fn ($q) => $q->where('period_id', $data['value']))
                        : $query
                    ),
                SelectFilter::make('result_type')
                    ->label(__('Result'))
                    ->relationship('result.result_name', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('export_result')
                    ->label(__('Result PDF'))
                    ->icon(Heroicon::OutlinedDocumentArrowDown)
                    ->visible(fn (Enrollment $record) => $record->result !== null)
                    ->action(function (Enrollment $record) {
                        $service = app(CertificatesInterface::class);

                        return $service->exportResult($record);
                    }),
                Action::make('export_certificate')
                    ->label(__('Certificate'))
                    ->icon(Heroicon::OutlinedAcademicCap)
                    ->visible(fn (Enrollment $record) => $record->result !== null)
                    ->action(function (Enrollment $record) {
                        $service = app(CertificatesInterface::class);

                        return $service->exportCertificate($record);
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ResultExporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(ResultExporter::class),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageResults::route('/'),
            'view' => ViewResult::route('/{record}'),
        ];
    }
}
