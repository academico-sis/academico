<?php

namespace App\Filament\Resources\Partners;

use App\Filament\Concerns\AuthorizesWritesWithPermissions;
use App\Filament\Resources\Partners\Pages\ManagePartners;
use App\Models\Partner;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartnerResource extends Resource
{
    use AuthorizesWritesWithPermissions;

    protected static string $editPermission = 'courses.edit';

    protected static string $deletePermission = 'courses.delete';

    protected static ?string $model = Partner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?int $navigationSort = 150;

    public static function canAccess(): bool
    {
        return (auth()->user()?->can('courses.view') ?? false)
            && (bool) config('settings.external_courses_enabled');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Course');
    }

    public static function getModelLabel(): string
    {
        return __('Partnership');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Partnerships');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->minLength(1)
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('send_report_on')
                    ->label(__('Send report on ... of the month'))
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(28),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('send_report_on')
                    ->label(__('Report Day'))
                    ->numeric()
                    ->visibleFrom('md'),
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
            'index' => ManagePartners::route('/'),
        ];
    }
}
