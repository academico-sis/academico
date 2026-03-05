<?php

namespace App\Filament\Resources\Periods;

use App\Filament\Resources\Periods\Pages\ListPeriods;
use App\Models\Config;
use App\Models\Period;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PeriodResource extends Resource
{
    protected static ?string $model = Period::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }

    public static function getModelLabel(): string
    {
        return __('Period');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Periods');
    }

    protected static ?string $cluster = \App\Filament\Clusters\Settings\SettingsCluster::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('year_id')
                    ->label(__('Year'))
                    ->relationship('year', 'name')
                    ->required()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->minLength(1)
                            ->maxLength(10),
                    ]),
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->minLength(1)
                    ->maxLength(40)
                    ->unique(ignoreRecord: true),
                DatePicker::make('start')
                    ->label(__('Start Date'))
                    ->required(),
                DatePicker::make('end')
                    ->label(__('End Date'))
                    ->required(),
                Toggle::make('archived')
                    ->label(__('Archived')),
            ]);
    }

    public static function table(Table $table): Table
    {
        $currentPeriodId = Config::where('name', 'current_period')->first()?->value;
        $enrollmentPeriodId = Config::where('name', 'default_enrollment_period')->first()?->value;

        return $table
            ->columns([
                // Mobile: stacked period info
                TextColumn::make('mobile_period')
                    ->label(__('Period'))
                    ->state(fn ($record) => $record->name)
                    ->description(fn ($record) => collect([$record->year?->name, $record->start?->format('M j, Y').' → '.$record->end?->format('M j, Y')])->filter()->implode(' · '))
                    ->searchable(query: fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
                    ->wrap()
                    ->hiddenFrom('md'),
                // Desktop columns
                TextColumn::make('year.name')
                    ->label(__('Year'))
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('start')
                    ->label(__('Start Date'))
                    ->date()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('end')
                    ->label(__('End Date'))
                    ->date()
                    ->sortable()
                    ->visibleFrom('lg'),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->state(function (Period $record) use ($currentPeriodId, $enrollmentPeriodId) {
                        $badges = [];
                        if ((string) $record->id === (string) $currentPeriodId) {
                            $badges[] = __('Current');
                        }
                        if ((string) $record->id === (string) $enrollmentPeriodId) {
                            $badges[] = __('Enrollment');
                        }

                        return empty($badges) ? null : implode(' | ', $badges);
                    })
                    ->placeholder('')
                    ->color(fn (Period $record) => match (true) {
                        (string) $record->id === (string) $currentPeriodId && (string) $record->id === (string) $enrollmentPeriodId => 'success',
                        (string) $record->id === (string) $currentPeriodId => 'success',
                        (string) $record->id === (string) $enrollmentPeriodId => 'info',
                        default => null,
                    }),
                IconColumn::make('archived')
                    ->label(__('Archived'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('year_id')
                    ->relationship('year', 'name')
                    ->label(__('Year'))
                    ->preload(),
            ])
            ->recordActions([
                Action::make('setAsCurrentPeriod')
                    ->label(__('Set as current'))
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (Period $record) => (string) $record->id === (string) $currentPeriodId)
                    ->action(function (Period $record) {
                        Config::updateOrCreate(['name' => 'current_period'], ['value' => $record->id]);

                        Notification::make()
                            ->title(__('Current period updated'))
                            ->body($record->name)
                            ->success()
                            ->send();
                    }),
                Action::make('setAsEnrollmentPeriod')
                    ->label(__('Set as enrollment period'))
                    ->icon('heroicon-m-academic-cap')
                    ->color('info')
                    ->requiresConfirmation()
                    ->hidden(fn (Period $record) => (string) $record->id === (string) $enrollmentPeriodId)
                    ->action(function (Period $record) {
                        Config::updateOrCreate(['name' => 'default_enrollment_period'], ['value' => $record->id]);

                        Notification::make()
                            ->title(__('Enrollment period updated'))
                            ->body($record->name)
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                ActionGroup::make([
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPeriods::route('/'),
        ];
    }
}
