<?php

namespace App\Filament\Resources\Settings\Skills;

use App\Filament\Resources\Settings\Skills\Pages\ManageSkills;
use App\Models\Skills\Skill;
use App\Models\Skills\SkillType;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?int $navigationSort = 7;

    protected static ?string $cluster = \App\Filament\Clusters\Settings\SettingsCluster::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }

    public static function getModelLabel(): string
    {
        return __('Skill');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Skills');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('skill_type_id')
                    ->label(__('Type'))
                    ->options(SkillType::pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->minLength(1)
                    ->maxLength(1000)
                    ->unique(ignoreRecord: true),
                Select::make('level_id')
                    ->label(__('Level'))
                    ->relationship('level', 'name')
                    ->required()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('skillType.name')
                    ->label(__('Type'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('level.name')
                    ->label(__('Level'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('level_id')
                    ->label(__('Level'))
                    ->relationship('level', 'name'),
                SelectFilter::make('skill_type_id')
                    ->label(__('Type'))
                    ->options(SkillType::pluck('name', 'id')),
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
            'index' => ManageSkills::route('/'),
        ];
    }
}
