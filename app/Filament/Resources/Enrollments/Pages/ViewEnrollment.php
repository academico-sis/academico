<?php

namespace App\Filament\Resources\Enrollments\Pages;

use App\Filament\Resources\Enrollments\EnrollmentResource;
use App\Filament\Resources\Enrollments\RelationManagers\EnrollmentCommentsRelationManager;
use App\Filament\Resources\Enrollments\RelationManagers\ScholarshipsRelationManager;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;

class ViewEnrollment extends ViewRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit_price')
                ->label(__('Price'))
                ->icon('heroicon-o-currency-dollar')
                ->fillForm(fn () => ['total_price' => $this->record->total_price])
                ->form([
                    TextInput::make('total_price')
                        ->label(__('Price'))
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->step(0.01)
                        ->prefix(config('academico.currency_position') === 'before' ? config('academico.currency_symbol') : null)
                        ->suffix(config('academico.currency_position') === 'after' ? config('academico.currency_symbol') : null),
                ])
                ->action(function (array $data) {
                    $this->record->update(['total_price' => $data['total_price']]);
                    $this->refreshFormData(['total_price']);
                }),

            Action::make('edit_status')
                ->label(__('Status'))
                ->icon('heroicon-o-arrow-path')
                ->fillForm(fn () => ['status_id' => $this->record->status_id])
                ->form([
                    Select::make('status_id')
                        ->label(__('Status'))
                        ->relationship('enrollmentStatus', 'name')
                        ->required()
                        ->preload(),
                ])
                ->action(function (array $data) {
                    $this->record->update(['status_id' => $data['status_id']]);
                    $this->refreshFormData(['status_id']);
                }),

            Action::make('change_course')
                ->label(__('Change course'))
                ->icon('heroicon-o-academic-cap')
                ->url(EnrollmentResource::getUrl('change-course', ['record' => $this->record])),

            Action::make('cancel')
                ->label(__('Cancel Enrollment'))
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalDescription('This will cancel the enrollment and delete associated attendance records. This action cannot be undone.')
                ->action(function () {
                    $this->record->cancel();
                    $this->redirect(EnrollmentResource::getUrl('index'));
                }),
        ];
    }

    public function content(Schema $schema): Schema
    {
        $ownerRecord = $this->getRecord();
        $livewireData = ['ownerRecord' => $ownerRecord, 'pageClass' => static::class];

        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 2])
                    ->schema([
                        $this->getInfolistContentComponent()
                            ->columnSpan(1),
                        Livewire::make(EnrollmentCommentsRelationManager::class, [
                            ...$livewireData,
                            ...EnrollmentCommentsRelationManager::getDefaultProperties(),
                        ])->key('comments')
                            ->columnSpan(1),
                    ]),
                Livewire::make(ScholarshipsRelationManager::class, [
                    ...$livewireData,
                    ...ScholarshipsRelationManager::getDefaultProperties(),
                ])->key('scholarships'),
            ]);
    }
}
