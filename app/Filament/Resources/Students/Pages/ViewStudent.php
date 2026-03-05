<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\Students\RelationManagers\ContactsRelationManager;
use App\Filament\Resources\Students\RelationManagers\EnrollmentsRelationManager;
use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function content(Schema $schema): Schema
    {
        $ownerRecord = $this->getRecord();
        $livewireData = ['ownerRecord' => $ownerRecord, 'pageClass' => static::class];

        return $schema->components([
            Grid::make(['default' => 1, 'lg' => 2])->schema([
                $this->getInfolistContentComponent()->columnSpan(1),
                Livewire::make(CommentsRelationManager::class, [
                    ...$livewireData,
                    ...CommentsRelationManager::getDefaultProperties(),
                ])->key('comments')->columnSpan(1),
            ]),
            Livewire::make(ContactsRelationManager::class, [
                ...$livewireData,
                ...ContactsRelationManager::getDefaultProperties(),
            ])->key('contacts'),
            Livewire::make(EnrollmentsRelationManager::class, [
                ...$livewireData,
                ...EnrollmentsRelationManager::getDefaultProperties(),
            ])->key('enrollments'),
        ]);
    }
}
