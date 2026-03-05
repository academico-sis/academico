<?php

namespace App\Filament\Resources\Teachers;

use App\Filament\Resources\Teachers\Pages\CreateTeacher;
use App\Filament\Resources\Teachers\Pages\EditTeacher;
use App\Filament\Resources\Teachers\Pages\ListTeachers;
use App\Models\Teacher;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?int $navigationSort = 510;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('hr.view') ?? false;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Organization');
    }

    public static function getModelLabel(): string
    {
        return __('Teacher');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Teachers');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('firstname')
                    ->label(__('First name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('lastname')
                    ->label(__('Last name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('max_week_hours')
                    ->label(__('Max weekly hours'))
                    ->numeric()
                    ->step(0.01)
                    ->nullable(),
                DatePicker::make('hired_at')
                    ->label(__('Hire Date'))
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Mobile: stacked teacher info
                TextColumn::make('mobile_name')
                    ->label(__('Teacher'))
                    ->state(fn ($record) => $record->user?->lastname.', '.$record->user?->firstname)
                    ->description(fn ($record) => $record->user?->email)
                    ->searchable(query: fn ($query, $search) => $query->whereHas('user', fn ($q) => $q->where('lastname', 'like', "%{$search}%")->orWhere('firstname', 'like', "%{$search}%")))
                    ->sortable(query: fn ($query, $direction) => $query->join('users', 'teachers.user_id', '=', 'users.id')->orderBy('users.lastname', $direction))
                    ->wrap()
                    ->hiddenFrom('md'),
                // Desktop columns
                TextColumn::make('id')
                    ->label(__('ID Number'))
                    ->sortable(),
                TextColumn::make('user.lastname')
                    ->label(__('Last name'))
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('user.firstname')
                    ->label(__('First name'))
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('user.email')
                    ->label(__('Email'))
                    ->wrap()
                    ->width('180px')
                    ->searchable()
                    ->visibleFrom('md'),
                TextColumn::make('max_week_hours')
                    ->label(__('Max hours/week'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('hired_at')
                    ->label(__('Hire Date'))
                    ->date()
                    ->sortable()
                    ->visibleFrom('md'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'active' => __('Active'),
                        'inactive' => __('Inactive'),
                    ])
                    ->default('active')
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'active' => $query->whereNull('deleted_at'),
                            'inactive' => $query->whereNotNull('deleted_at'),
                            default => $query->withTrashed(),
                        };
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeachers::route('/'),
            'create' => CreateTeacher::route('/create'),
            'edit' => EditTeacher::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
