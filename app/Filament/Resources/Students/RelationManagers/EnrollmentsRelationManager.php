<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Filament\Resources\Enrollments\EnrollmentResource;
use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.name')
                    ->label(__('Course'))
                    ->searchable(),
                TextColumn::make('course.period.name')
                    ->label(__('Period')),
                TextColumn::make('enrollmentStatus.name')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn ($record): ?string => $record->enrollmentStatus?->color),
                TextColumn::make('total_price')
                    ->label(__('Price'))
                    ->money(config('academico.currency_code', 'USD')),
                TextColumn::make('created_at')
                    ->label(__('Enrolled'))
                    ->date(),
            ])
            ->recordUrl(fn ($record) => EnrollmentResource::getUrl('view', ['record' => $record]))
            ->headerActions([
                Action::make('enroll')
                    ->label(__('Enroll in a course'))
                    ->icon('heroicon-o-plus-circle')
                    ->url(fn () => StudentResource::getUrl('enroll', ['record' => $this->getOwnerRecord()])),
            ]);
    }
}
