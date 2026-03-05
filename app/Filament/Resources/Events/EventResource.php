<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\ListEvents;
use App\Models\Event;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?int $navigationSort = 430;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('courses.view') ?? false;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Organization');
    }

    public static function getModelLabel(): string
    {
        return __('Event');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Events');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->minLength(1)
                    ->maxLength(255),
                Select::make('course_id')
                    ->label(__('Course'))
                    ->relationship('course', 'name')
                    ->preload()
                    ->searchable()
                    ->nullable(),
                Select::make('teacher_id')
                    ->label(__('Teacher'))
                    ->relationship('teacher', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->preload()
                    ->searchable()
                    ->nullable(),
                Select::make('room_id')
                    ->label(__('Room'))
                    ->relationship('room', 'name')
                    ->preload()
                    ->nullable(),
                DateTimePicker::make('start')
                    ->label(__('Start'))
                    ->required(),
                DateTimePicker::make('end')
                    ->label(__('End'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Mobile: stacked event info
                TextColumn::make('mobile_event')
                    ->label(__('Event'))
                    ->state(fn ($record) => $record->name)
                    ->description(fn ($record) => collect([$record->teacher?->name, $record->room?->name])->filter()->implode(' · '))
                    ->searchable(query: fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
                    ->wrap()
                    ->hiddenFrom('md'),
                TextColumn::make('mobile_schedule')
                    ->label(__('Schedule'))
                    ->state(fn ($record) => $record->start?->format('M j, Y H:i'))
                    ->description(fn ($record) => $record->end?->format('M j, Y H:i'))
                    ->hiddenFrom('md'),
                // Desktop columns
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->wrap()
                    ->width('180px')
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('course.name')
                    ->label(__('Course'))
                    ->wrap()
                    ->width('180px')
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('volume')
                    ->label(__('Hours'))
                    ->formatStateUsing(fn ($state): string => number_format($state, 1).'h')
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('teacher.name')
                    ->label(__('Teacher'))
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('room.name')
                    ->label(__('Room'))
                    ->sortable()
                    ->visibleFrom('lg'),
                TextColumn::make('start')
                    ->label(__('Start'))
                    ->dateTime()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('end')
                    ->label(__('End'))
                    ->dateTime()
                    ->sortable()
                    ->visibleFrom('lg'),
            ])
            ->defaultSort('start', 'desc')
            ->filters([
                Filter::make('date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->where('start', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->where('start', '<=', $date.' 23:59:59'));
                    }),
                TernaryFilter::make('orphan')
                    ->label(__('No course'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNull('course_id'),
                        false: fn (Builder $query) => $query->whereNotNull('course_id'),
                    ),
                TernaryFilter::make('unassigned')
                    ->label(__('No teacher'))
                    ->queries(
                        true: fn (Builder $query) => $query->unassigned(),
                        false: fn (Builder $query) => $query->whereNotNull('teacher_id'),
                    ),
                SelectFilter::make('teacher_id')
                    ->relationship('teacher', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->label(__('Teacher'))
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
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
            'index' => ListEvents::route('/'),
        ];
    }
}
