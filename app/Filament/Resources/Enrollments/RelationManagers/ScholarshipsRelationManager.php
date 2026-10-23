<?php

namespace App\Filament\Resources\Enrollments\RelationManagers;

use App\Filament\Resources\Enrollments\EnrollmentResource;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ScholarshipsRelationManager extends RelationManager
{
    protected static string $relationship = 'scholarships';

    /**
     * Attaching and detaching scholarships changes the enrollment.
     */
    public function isReadOnly(): bool
    {
        return ! EnrollmentResource::canEdit($this->getOwnerRecord());
    }

    public static function getTitle(mixed $ownerRecord, string $pageClass): string
    {
        return __('Scholarships');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name')),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                DetachAction::make(),
            ]);
    }
}
