<?php

namespace App\Filament\Resources\ScheduledPayments;

use App\Filament\Resources\ScheduledPayments\Pages\ManageScheduledPayments;
use App\Models\ScheduledPayment;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ScheduledPaymentResource extends Resource
{
    protected static ?string $model = ScheduledPayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?int $navigationSort = 384;

    public static function canAccess(): bool
    {
        return (bool) config('invoicing.allow_scheduled_payments');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Accounting');
    }

    public static function getModelLabel(): string
    {
        return __('Scheduled Payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Scheduled Payments');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['enrollment.student.user', 'enrollment.course']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('value')
                    ->label(__('Amount'))
                    ->numeric()
                    ->required()
                    ->step(0.01)
                    ->prefix(config('academico.currency_position') === 'before' ? config('academico.currency_symbol') : null)
                    ->suffix(config('academico.currency_position') === 'after' ? config('academico.currency_symbol') : null),
                DatePicker::make('date')
                    ->label(__('Date'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Mobile: stacked student + date
                TextColumn::make('mobile_student')
                    ->label(__('Student'))
                    ->state(fn ($record) => $record->enrollment?->student?->user?->lastname.' '.$record->enrollment?->student?->user?->firstname)
                    ->description(fn ($record) => $record->date?->format('M j, Y'))
                    ->searchable(query: fn ($query, $search) => $query->whereHas('enrollment.student.user', fn ($q) => $q->where('lastname', 'like', "%{$search}%")->orWhere('firstname', 'like', "%{$search}%")))
                    ->wrap()
                    ->hiddenFrom('md'),
                // Desktop columns
                TextColumn::make('enrollment.student.user.lastname')
                    ->label(__('Last name'))
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('enrollment.student.user.firstname')
                    ->label(__('First name'))
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('enrollment.student.user.email')
                    ->label(__('Email'))
                    ->wrap()
                    ->width('180px')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visibleFrom('md'),
                TextColumn::make('enrollment.course.name')
                    ->label(__('Course'))
                    ->wrap()
                    ->width('180px')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visibleFrom('md'),
                TextColumn::make('value')
                    ->label(__('Amount'))
                    ->money(config('academico.currency_code', 'USD'))
                    ->sortable(),
                TextColumn::make('status_type_name')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        __('Paid') => 'success',
                        __('Pending') => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable()
                    ->visibleFrom('md'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        '1' => __('Pending'),
                        '2' => __('Paid'),
                    ]),
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
            'index' => ManageScheduledPayments::route('/'),
        ];
    }
}
