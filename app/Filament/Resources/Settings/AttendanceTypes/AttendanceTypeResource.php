<?php

namespace App\Filament\Resources\Settings\AttendanceTypes;

use App\Filament\Resources\Settings\AttendanceTypes\Pages\ManageAttendanceTypes;
use App\Models\AttendanceType;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendanceTypeResource extends Resource
{
    protected static ?string $model = AttendanceType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $cluster = \App\Filament\Clusters\Settings\SettingsCluster::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Organization');
    }

    public static function getModelLabel(): string
    {
        return __('Attendance Type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Attendance Types');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required(),
                ColorPicker::make('color')
                    ->label(__('Color'))
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                ColorColumn::make('color')
                    ->label(__('Color')),
            ])
            ->filters([
                //
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
            'index' => ManageAttendanceTypes::route('/'),
        ];
    }
}
