<?php

namespace App\Filament\Resources\ExternalCourses;

use App\Filament\Resources\ExternalCourses\Pages\ManageExternalCourses;
use App\Models\Course;
use App\Models\Period;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExternalCourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $slug = 'external-courses';

    protected static ?int $navigationSort = 110;

    public static function canAccess(): bool
    {
        return (bool) config('settings.external_courses_enabled');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Courses');
    }

    public static function getModelLabel(): string
    {
        return __('External Course');
    }

    public static function getPluralModelLabel(): string
    {
        return __('External Courses');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->external();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('partner_id')
                    ->label(__('Partnership'))
                    ->relationship('partner', 'name')
                    ->required()
                    ->preload()
                    ->searchable(),
                Select::make('rhythm_id')
                    ->label(__('Rhythm'))
                    ->relationship('rhythm', 'name')
                    ->preload()
                    ->searchable(),
                Select::make('level_id')
                    ->label(__('Level'))
                    ->relationship('level', 'name')
                    ->preload()
                    ->searchable(),
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required(),
                TextInput::make('volume')
                    ->label(__('Volume'))
                    ->numeric()
                    ->suffix('h'),
                TextInput::make('hourly_price')
                    ->label(__('Hourly Price'))
                    ->numeric()
                    ->prefix(config('academico.currency_symbol', '€')),
                Select::make('teacher_id')
                    ->label(__('Teacher'))
                    ->relationship('teacher', titleAttribute: 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->preload()
                    ->searchable(),
                Select::make('room_id')
                    ->label(__('Room'))
                    ->relationship('room', 'name')
                    ->preload()
                    ->searchable(),
                Select::make('period_id')
                    ->label(__('Period'))
                    ->relationship('period', 'name')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->default(fn () => Period::get_default_period()?->id),
                Select::make('campus_id')
                    ->label(__('Campus'))
                    ->relationship('campus', 'name')
                    ->preload(),
                DatePicker::make('start_date')
                    ->label(__('Start Date')),
                DatePicker::make('end_date')
                    ->label(__('End Date')),
                TextInput::make('head_count')
                    ->label(__('Head Count'))
                    ->numeric()
                    ->default(0),
                TextInput::make('new_students')
                    ->label(__('Students to count in year total'))
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('partner.name')
                    ->label(__('Partnership'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('rhythm.name')
                    ->label(__('Rhythm'))
                    ->sortable(),
                TextColumn::make('level.name')
                    ->label(__('Level'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('volume')
                    ->label(__('Volume'))
                    ->suffix('h')
                    ->numeric(),
                TextColumn::make('hourly_price')
                    ->label(__('Price'))
                    ->money(config('academico.currency_code', 'EUR')),
                TextColumn::make('teacher.user.name')
                    ->label(__('Teacher')),
                TextColumn::make('room.name')
                    ->label(__('Room')),
                TextColumn::make('course_times')
                    ->label(__('Schedule'))
                    ->limit(50),
                TextColumn::make('head_count')
                    ->label(__('Students'))
                    ->numeric(),
                TextColumn::make('new_students')
                    ->label(__('Year Students'))
                    ->numeric(),
                TextColumn::make('start_date')
                    ->label(__('Start Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(__('End Date'))
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('partner_id')
                    ->label(__('Partnership'))
                    ->relationship('partner', 'name'),
                SelectFilter::make('rhythm_id')
                    ->label(__('Rhythm'))
                    ->relationship('rhythm', 'name'),
                SelectFilter::make('teacher_id')
                    ->label(__('Teacher'))
                    ->relationship('teacher', titleAttribute: 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                SelectFilter::make('level_id')
                    ->label(__('Level'))
                    ->relationship('level', 'name'),
                SelectFilter::make('period_id')
                    ->label(__('Period'))
                    ->relationship('period', 'name')
                    ->default(fn () => Period::get_default_period()?->id),
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
            'index' => ManageExternalCourses::route('/'),
        ];
    }
}
